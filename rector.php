<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\PHPUnit100\Rector\Class_\AddProphecyTraitRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Symfony\Set\SymfonySetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpSets()
    ->withPreparedSets(
        true,
        true,
        false,
        true
    )
    ->withImportNames(
        true,
        true,
        false
    )
    ->withRules([
        AddProphecyTraitRector::class,
    ])
    ->withSets([
        PHPUnitSetList::PHPUNIT_90,
        SymfonySetList::SYMFONY_44,
    ]);
