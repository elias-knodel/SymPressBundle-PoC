<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use SymPress\Bundle\Admin\AdminPageRegistrar;
use SymPress\Bundle\Ajax\AjaxRegistrar;
use SymPress\Bundle\Cron\CronRegistrar;
use SymPress\Bundle\Cron\CronScheduleProvider;
use SymPress\Bundle\Hook\HookRegistrar;
use SymPress\Bundle\Hook\HookRegistry;
use SymPress\Bundle\Rest\RestRouteRegistrar;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    // Core registry
    $services->set(HookRegistry::class);

    // Registrars — marked public so plugin bootstrap can fetch them from the compiled container
    $services->set(HookRegistrar::class)->public();
    $services->set(RestRouteRegistrar::class)->public();
    $services->set(AdminPageRegistrar::class)->public();
    $services->set(CronRegistrar::class)->public();
    $services->set(CronScheduleProvider::class)->public();
    $services->set(AjaxRegistrar::class)->public();
};
