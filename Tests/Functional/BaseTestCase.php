<?php

declare(strict_types=1);

namespace JMS\JobQueueBundle\Tests\Functional;

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManager;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class BaseTestCase extends WebTestCase
{
    protected static function createKernel(array $options = array()): KernelInterface
    {
        $config = isset($options['config']) ? $options['config'] : 'default.yml';

        return new AppKernel($config);
    }

    protected final function importDatabaseSchema()
    {
        foreach (self::$kernel->getContainer()->get('doctrine')->getManagers() as $em) {
            $this->importSchemaForEm($em);
        }
    }

    private function importSchemaForEm(EntityManager $em)
    {
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        if (!empty($metadata)) {
            $schemaTool = new SchemaTool($em);
            $schemaTool->createSchema($metadata);
        }
    }
}
