<?php

declare(strict_types=1);

namespace SymPress\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use SymPress\Bundle\DependencyInjection\CompilerPass\HookDiscoveryCompilerPass;

/**
 * SymPress Bundle — Attribute-driven WordPress framework with Symfony DI.
 *
 * Provides automatic hook registration, REST API routes,
 * cron jobs, AJAX handlers, and more from PHP 8.4 attributes.
 *
 * Example — register in your plugin bootstrap:
 * ```php
 * $containerBuilder->registerExtension(new SymPressExtension());
 * $bundle = new SymPressBundle();
 * $bundle->build($containerBuilder);
 * ```
 */
final class SymPressBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new HookDiscoveryCompilerPass());
    }
}
