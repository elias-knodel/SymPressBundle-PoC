<?php

declare(strict_types=1);

namespace SymPress\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * Loads and configures the SymPress bundle services.
 */
final class SymPressExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        /** @var array{hook_paths: list<string>} $config */
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('sympress.hook_paths', $config['hook_paths']);

        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__, 2).'/config'));
        $loader->load('services.php');
    }

    public function getAlias(): string
    {
        return 'sym_press';
    }
}
