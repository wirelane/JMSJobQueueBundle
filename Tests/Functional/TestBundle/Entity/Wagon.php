<?php

declare(strict_types=1);

namespace JMS\JobQueueBundle\Tests\Functional\TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'wagons')]
#[ORM\Entity]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Wagon
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Train::class)]
    public $train;

    #[ORM\Column(type: 'string')]
    public $state = 'new';
}
