<?php

declare(strict_types=1);

namespace JMS\JobQueueBundle\Twig;

interface LinkGeneratorInterface
{
    function supports($entity);
    function generate($entity);
    function getLinkname($entity);
}
