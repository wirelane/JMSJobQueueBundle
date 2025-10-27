<?php

declare(strict_types=1);


use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Doctrine\Orm214\Rector\Param\ReplaceLifecycleEventArgsByDedicatedEventArgsRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNativeCallRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    // ✅ Directories to process
    $rectorConfig->paths([
        __DIR__ . '/Command',
        __DIR__ . '/Controller',
        __DIR__ . '/Entity',
        __DIR__ . '/DependencyInjection',
        __DIR__ . '/Event',
        __DIR__ . '/Twig',
        __DIR__ . '/Tests',
        __DIR__ . '/Resources',
    ]);

    // ✅ Skip vendor + irrelevant dirs
    $rectorConfig->skip([
        __DIR__ . '/vendor',
        __DIR__ . '/var',
        __DIR__ . '/.git',
        __DIR__ . '/Dockerfile',
        __DIR__ . '/docker-compose.yml',
        __DIR__ . '/node_modules',
    ]);

    // ✅ Optional: clean import settings
    $rectorConfig->importNames(false, true);

    // ✅ Individual rules
    $rectorConfig->rules([
        ReplaceLifecycleEventArgsByDedicatedEventArgsRector::class,
        RestoreDefaultNullToNullableTypePropertyRector::class,
        ReturnTypeFromStrictNativeCallRector::class,
        DeclareStrictTypesRector::class,
    ]);

    // ✅ Doctrine & Symfony sets
    $rectorConfig->sets([
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
        DoctrineSetList::GEDMO_ANNOTATIONS_TO_ATTRIBUTES,
        SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES, // optional if bundle uses Symfony attrs
    ]);
};
