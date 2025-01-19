<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector\FuncCall\CompactToVariablesRector;
use Rector\Config\RectorConfig;
use Rector\Privatization\Rector\ClassMethod\PrivatizeFinalClassMethodRector;
use Rector\Set\ValueObject\SetList;
use Rector\Transform\Rector\String_\StringToClassConstantRector;
use Rector\ValueObject\PhpVersion;
use RectorLaravel\Set\LaravelLevelSetList;

return static function (RectorConfig $rectorConfig): void {
    // Paths to analyze
    $rectorConfig->paths([
        __DIR__.'/app',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/resources',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ]);

    // Skip specific rules
    $rectorConfig->skip([
        // CompactToVariablesRector::class,
        StringToClassConstantRector::class => [
            __DIR__.'/routes/web.php',
            __DIR__.'/app/Providers/AppServiceProvider.php',
        ],
        PrivatizeFinalClassMethodRector::class => [
            __DIR__.'/app/Filament/Admin/Resources/RoleResource/Pages/CreateRole.php',
            __DIR__.'/app/Filament/Admin/Resources/RoleResource/Pages/EditRole.php',
        ],
    ]);

    // Enable caching for Rector
    $rectorConfig->cacheDirectory(__DIR__.'/storage/rector');
    $rectorConfig->cacheClass(FileCacheStorage::class);

    // Apply sets for Laravel and general code quality
    $rectorConfig->sets([
        LaravelLevelSetList::UP_TO_LARAVEL_110,
        SetList::PHP_83,
        SetList::DEAD_CODE,
        SetList::CODE_QUALITY,
        SetList::TYPE_DECLARATION,
        SetList::PRIVATIZATION,
        SetList::EARLY_RETURN,
        SetList::STRICT_BOOLEANS,
    ]);

    // Define PHP version for Rector
    $rectorConfig->phpVersion(PhpVersion::PHP_83);
};
