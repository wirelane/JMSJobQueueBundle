<?php

declare(strict_types=1);

namespace JMS\JobQueueBundle\Tests\Functional;

use JMS\JobQueueBundle\Entity\Job;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class CronTest extends BaseTestCase
{
    /** @var Application */
    private $app;


    public function testSchedulesCommands()
    {
        $output = $this->doRun(array('--min-job-interval' => 1, '--max-runtime' => 12));
        $this->assertEquals(2, substr_count($output, 'Scheduling command scheduled-every-few-seconds'), $output);
    }

    protected function setUp(): void
    {
        $this->createClient(array('config' => 'persistent_db.yml'));

        if (is_file($databaseFile = self::$kernel->getCacheDir().'/database.sqlite')) {
            unlink($databaseFile);
        }

        $this->importDatabaseSchema();

        $this->app = new Application(self::$kernel);
        $this->app->setAutoExit(false);
        $this->app->setCatchExceptions(false);

        self::$kernel->getContainer()->get('doctrine')->getManagerForClass(Job::class);
    }

    private function doRun(array $args = array())
    {
        array_unshift($args, 'jms-job-queue:schedule');
        $output = new MemoryOutput();
        $this->app->run(new ArrayInput($args), $output);

        return $output->getOutput();
    }

}
