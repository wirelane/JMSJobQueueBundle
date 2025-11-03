<?php

declare(strict_types=1);

namespace JMS\JobQueueBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'jms_cron_jobs')]
#[ORM\Entity]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class CronJob
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 200, unique: true)]
    private ?string $command = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, name: 'lastRunAt')]
    private ?\DateTimeInterface $lastRunAt = null;

    public function __construct($command)
    {
        $this->command = $command;
        $this->lastRunAt = new \DateTime();
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getLastRunAt()
    {
        return $this->lastRunAt;
    }
}
