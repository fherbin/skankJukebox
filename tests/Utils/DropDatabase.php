<?php

namespace App\Tests\Utils;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

trait DropDatabase
{
    public function dropDatabase(): EntityManagerInterface
    {
        /** @var ?Registry $doctrine */
        $doctrine = self::getContainer()->get('doctrine');
        if (!$doctrine) {
            throw new \Exception('doctrine service not found');
        }
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $doctrine->getManager();
        $schemaTool = new SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        return $entityManager;
    }
}
