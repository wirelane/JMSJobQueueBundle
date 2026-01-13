<?php

declare(strict_types=1);

namespace JMS\JobQueueBundle\Command;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use JMS\JobQueueBundle\Console\CronCommand;
use JMS\JobQueueBundle\Cron\CommandScheduler;
use JMS\JobQueueBundle\Cron\JobScheduler;
use JMS\JobQueueBundle\Entity\CronJob;
use JMS\JobQueueBundle\Entity\Job;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('jms-job-queue:schedule')]
class ScheduleCommand extends Command
{

    public function __construct(
        private readonly ManagerRegistry $registry,
        /** @var JobScheduler[] */
        private readonly iterable $schedulers,
        private readonly iterable $cronCommands
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Schedules jobs at defined intervals')
            ->addOption('max-runtime', null, InputOption::VALUE_REQUIRED, 'The maximum runtime of this command.', 3600)
            ->addOption('min-job-interval', null, InputOption::VALUE_REQUIRED, 'The minimum time between schedules jobs in seconds.', 5)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $maxRuntime = $input->getOption('max-runtime');
        if ($maxRuntime > 300) {
            $maxRuntime += random_int(0, (integer)($input->getOption('max-runtime') * 0.05));
        }
        if ($maxRuntime <= 0) {
            throw new \RuntimeException('Max. runtime must be greater than zero.');
        }

        $minJobInterval = (integer)$input->getOption('min-job-interval');
        if ($minJobInterval <= 0) {
            throw new \RuntimeException('Min. job interval must be greater than zero.');
        }

        $jobSchedulers = $this->populateJobSchedulers();
        if (empty($jobSchedulers)) {
            $output->writeln('No job schedulers found, exiting...');

            return 0;
        }
        /** @var EntityManager $em */
        $em = $this->registry->getManagerForClass(CronJob::class);
        $jobsLastRunAt = $this->populateJobsLastRunAt($em, $jobSchedulers);

        $startedAt = time();
        while (true) {
            $lastRunAt = microtime(true);
            $now = time();
            if ($now - $startedAt > $maxRuntime) {
                $output->writeln('Max. runtime reached, exiting...');
                break;
            }

            $jobsLastRunAt = $this->scheduleJobs($output, $jobSchedulers, $jobsLastRunAt);

            $timeToWait = microtime(true) - $lastRunAt + $minJobInterval;
            if ($timeToWait > 0) {
                usleep((int) ($timeToWait * 1E6));
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @param JobScheduler[] $jobSchedulers
     * @param \DateTime[] $jobsLastRunAt
     */
    private function scheduleJobs(OutputInterface $output, array $jobSchedulers, array $jobsLastRunAt): array
    {
        foreach ($jobSchedulers as $name => $scheduler) {
            $lastRunAt = $jobsLastRunAt[$name];

            if ( ! $scheduler->shouldSchedule($name, $lastRunAt)) {
                continue;
            }

            list($success, $newLastRunAt) = $this->acquireLock($name, $lastRunAt);
            $jobsLastRunAt[$name] = $newLastRunAt;

            if ($success) {
                $output->writeln('Scheduling command '.$name);
                $job = $scheduler->createJob($name, $lastRunAt);
                $em = $this->registry->getManagerForClass(Job::class);
                $em->persist($job);
                $em->flush();
            }
        }

        return $jobsLastRunAt;
    }

    private function acquireLock($commandName, \DateTime $lastRunAt): array
    {
        /** @var EntityManager $em */
        $em = $this->registry->getManagerForClass(CronJob::class);
        $con = $em->getConnection();

        $now = new \DateTime();
        $affectedRows = $con->executeStatement(
            "UPDATE jms_cron_jobs SET lastRunAt = :now WHERE command = :command AND lastRunAt = :lastRunAt",
            array(
                'now' => $now,
                'command' => $commandName,
                'lastRunAt' => $lastRunAt,
            ),
            array(
                'now' => 'datetime',
                'lastRunAt' => 'datetime',
            )
        );

        if ($affectedRows > 0) {
            return array(true, $now);
        }

        /** @var CronJob $cronJob */
        $cronJob = $em->createQuery("SELECT j FROM ".CronJob::class." j WHERE j.command = :command")
            ->setParameter('command', $commandName)
            ->setHint(Query::HINT_REFRESH, true)
            ->getSingleResult();

        return array(false, $cronJob->getLastRunAt());
    }

    private function populateJobSchedulers(): array
    {
        $schedulers = [];
        foreach ($this->schedulers as $scheduler) {
            foreach ($scheduler->getCommands() as $name) {
                $schedulers[$name] = $scheduler;
            }
        }

        foreach ($this->cronCommands as $command) {
            /** @var CronCommand $command */
            if ( ! $command instanceof Command) {
                throw new \RuntimeException('CronCommand should only be used on Symfony commands.');
            }

            $commandName = $command->getName();
            $schedulers[$commandName] = new CommandScheduler($commandName, $command);
        }

        return $schedulers;
    }

    private function populateJobsLastRunAt(EntityManager $em, array $jobSchedulers): array
    {
        $jobsLastRunAt = array();

        foreach ($em->getRepository(CronJob::class)->findAll() as $job) {
            /** @var CronJob $job */
            $jobsLastRunAt[$job->getCommand()] = $job->getLastRunAt();
        }

        foreach (array_keys($jobSchedulers) as $name) {
            if ( ! isset($jobsLastRunAt[$name])) {
                $job = new CronJob($name);
                $em->persist($job);
                $jobsLastRunAt[$name] = $job->getLastRunAt();
            }
        }
        $em->flush();

        return $jobsLastRunAt;
    }
}
