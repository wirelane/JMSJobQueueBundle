<?php

declare(strict_types=1);

namespace JMS\JobQueueBundle\Tests\Functional\TestBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'wagons')]
#[ORM\Entity]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Wagon
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Train::class)]
    public ?Train $train = null;

    #[ORM\Column(type: Types::STRING)]
    public ?string $state = 'new';
}
