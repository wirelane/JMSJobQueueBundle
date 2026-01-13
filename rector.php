<?php

declare(strict_types=1);


use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Doctrine\Orm214\Rector\Param\ReplaceLifecycleEventArgsByDedicatedEventArgsRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNativeCallRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return RectorConfig::configure()
    // paths & skips
    ->withPaths([
        __DIR__ . '/Command',
        __DIR__ . '/Controller',
        __DIR__ . '/Entity',
        __DIR__ . '/DependencyInjection',
        __DIR__ . '/Event',
        __DIR__ . '/Twig',
        __DIR__ . '/Tests',
        __DIR__ . '/Resources',
    ])
    ->withSkip([
        __DIR__ . '/vendor',
        __DIR__ . '/var',
        __DIR__ . '/.git',
        __DIR__ . '/Dockerfile',
        __DIR__ . '/docker-compose.yml',
        __DIR__ . '/node_modules',
    ])

    // imports (same behavior as your importNames(false, true))
    ->withImportNames(
        removeUnusedImports: true,
        importShortClasses: false
    )

    // individual rules
    ->withRules([
        ReplaceLifecycleEventArgsByDedicatedEventArgsRector::class,
        RestoreDefaultNullToNullableTypePropertyRector::class,
        ReturnTypeFromStrictNativeCallRector::class,
        DeclareStrictTypesRector::class,
    ])

    // any non-annotation sets you still want
    ->withSets([
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
    ])

    ->withAttributesSets(
        doctrine: true,
        symfony: true
    );
