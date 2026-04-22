<?php

declare(strict_types=1);

namespace SymPress\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Bundle configuration tree for `sym_press`.
 *
 * Example:
 * ```yaml
 * sym_press:
 *     hook_paths:
 *         - '%kernel.project_dir%/src'
 * ```
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sym_press');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('hook_paths')
                    ->defaultValue(['%kernel.project_dir%/src'])
                    ->scalarPrototype()->end()
                    ->info('Directories to scan for hook attributes.')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
