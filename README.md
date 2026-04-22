# SymPressBundle

## Description

This bundle adds WordPress Attributes to the Dependency Injection.  
It exists only as a Proof of Concept, however, I want to use it in a 
real project soon and give examples on how to work with it.

## Motivation

In the past I needed to develop WordPress Plugins for legacy Systems.  
However, coming from Symfony I missed all the cool stuff like Dependency Injection and Attribute Support of PHP 8.

### Creating a Plugin

`index.php`

```php
<?php

/**
 * Plugin Name: SymPress Plugin Example
 * Version: 1.0.0
 * Requires at least: 6.9
 * Tested up to: 6.9
 *
 * Text Domain: sympress-plugin-example
 * Domain Path: /lang/
 *
 * @package SymPress
 * @since 1.0.0
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/vendor/autoload.php';

use App\Kernel;
use SymPress\Bundle\Admin\AdminPageRegistrar;
use SymPress\Bundle\Ajax\AjaxRegistrar;
use SymPress\Bundle\Cron\CronRegistrar;
use SymPress\Bundle\Cron\CronScheduleProvider;
use SymPress\Bundle\Hook\HookRegistrar;
use SymPress\Bundle\Rest\RestRouteRegistrar;

add_action('plugins_loaded', static function (): void {
    $kernel = Kernel::getInstance();
    $kernel->boot();
    $container = $kernel->getContainer();

    // Actions, filters, shortcodes, asset enqueues
    $container->get(HookRegistrar::class)->registerHooks();

    // AJAX handlers
    $container->get(AjaxRegistrar::class)->register();

    // Custom cron schedules must be added before 'init'
    add_filter('cron_schedules', static function (array $schedules) use ($container): array {
        return $container->get(CronScheduleProvider::class)
            ->addSchedules($schedules);
    });

    // Post types + taxonomies + cron jobs on 'init'
    add_action('init', static function () use ($container): void {
        $container->get(CronRegistrar::class)->register();
    });

    // REST routes on 'rest_api_init'
    add_action('rest_api_init', static function () use ($container): void {
        $container->get(RestRouteRegistrar::class)->register();
    });

    // Admin pages on 'admin_menu'
    add_action('admin_menu', static function () use ($container): void {
        $container->get(AdminPageRegistrar::class)->register();
    });
});
```

`Environment.php`

```php
<?php

namespace App;

class Environment {
    public static function isDev(): bool
    {
        if (wp_get_environment_type() !== "production") {
            return true;
        }

        if (defined("WP_DEVELOPMENT_MODE") && !empty(WP_DEVELOPMENT_MODE)) {
            return true;
        }

        $siteUrl = get_site_url();
        if (str_contains($siteUrl, ".ddev.site")) {
            return true;
        }

        return false;
    }
}
```

`Kernel.php`

```php
<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private static self $instance;

    public static function getInstance(): static
    {
        $isDev = Environment::isDev();

        return static::$instance ??= new static(
            $isDev ? "dev" : "prod",
            $isDev
        );
    }
}
```
