<?php

declare(strict_types=1);

namespace URLCV\EmailFinder\Laravel;

use Illuminate\Support\ServiceProvider;

/**
 * Laravel service provider for the Email Finder package.
 *
 * Loads Blade views for frontend Alpine.js integration.
 */
class EmailFinderServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'email-finder');
    }
}
