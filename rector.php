<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\PHPUnit100\Rector\Class_\AddProphecyTraitRector;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Symfony\Set\SymfonyLevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // register a single rule
    $rectorConfig->rule(AddProphecyTraitRector::class);

    $rectorConfig->sets([
        PHPUnitSetList::PHPUNIT_90,
        PHPUnitLevelSetList::UP_TO_PHPUNIT_90,
        SymfonyLevelSetList::UP_TO_SYMFONY_44
    ]);
};
