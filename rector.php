<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;

return RectorConfig::configure()
    ->withPhpVersion(\Rector\ValueObject\PhpVersion::PHP_84)
    ->withSets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::NAMING,
        SetList::STRICT_BOOLEANS,
        SetList::TYPE_DECLARATION,
        SetList::INSTANCEOF_TO_STATIC_PROPERTY,
        LevelSetList::UP_TO_PHP_84,
    ])
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/config',
        __DIR__.'/tests',
    ])
    ->withSkip([
        __DIR__.'/vendor',
        __DIR__.'/build',
        __DIR__.'/node_modules',
    ])
    ->withRules([
        ExplicitNullableParamTypeRector::class,
    ])
    ->withImportNames(
        importShortClasses: false,
        importDocBlockNames: false,
    );