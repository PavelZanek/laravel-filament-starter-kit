<?php

declare(strict_types=1);

namespace App\Filament\Traits;

trait TranslatableResourceLabels
{
    /**
     * Get the navigation label using the translation prefix.
     */
    public static function getNavigationLabel(): string
    {
        return __(static::TRANSLATION_PREFIX.'.navigation_label');
    }

    /**
     * Get the navigation group using the translation prefix.
     */
    public static function getNavigationGroup(): string
    {
        return __(static::TRANSLATION_PREFIX.'.navigation_group');
    }

    /**
     * Get the breadcrumb using the translation prefix.
     */
    public static function getBreadcrumb(): string
    {
        return __(static::TRANSLATION_PREFIX.'.breadcrumb');
    }
}
