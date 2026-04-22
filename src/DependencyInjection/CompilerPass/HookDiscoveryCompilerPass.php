<?php

declare(strict_types=1);

namespace SymPress\Bundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use SymPress\Bundle\Admin\AdminPageRegistrar;
use SymPress\Bundle\Ajax\AjaxRegistrar;
use SymPress\Bundle\Attribute\Action;
use SymPress\Bundle\Attribute\AdminPage;
use SymPress\Bundle\Attribute\AjaxHandler;
use SymPress\Bundle\Attribute\AssetEnqueue;
use SymPress\Bundle\Attribute\CronJob;
use SymPress\Bundle\Attribute\Filter;
use SymPress\Bundle\Attribute\RestRoute;
use SymPress\Bundle\Attribute\Shortcode;
use SymPress\Bundle\Cron\CronRegistrar;
use SymPress\Bundle\Hook\HookRegistrar;
use SymPress\Bundle\Hook\HookRegistry;
use SymPress\Bundle\Rest\RestRouteRegistrar;

/**
 * Discovers classes with hook attributes and registers metadata
 * with the HookRegistry.
 *
 * Scans configured `hook_paths` for PHP classes bearing attributes like
 * #[Action], #[Filter], #[Shortcode], #[PostType], etc.
 */
final class HookDiscoveryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(HookRegistry::class)) {
            return;
        }

        /** @var array<string> $hookPaths */
        $hookPaths = $container->getParameter('sympress.hook_paths');
        $resolvedPaths = array_map(
            static fn (string $path): string => (string) $container->getParameterBag()->resolveValue($path),
            $hookPaths,
        );

        $classes = $this->discoverClasses($resolvedPaths);
        $definition = $container->getDefinition(HookRegistry::class);

        $actions = [];
        $filters = [];
        $shortcodes = [];
        $restRoutes = [];
        $crons = [];
        $adminPages = [];
        $ajaxHandlers = [];
        $assets = [];
        $postTypes = [];
        $taxonomies = [];

        $discoveredServiceIds = [];

        foreach ($classes as $className) {
            $reflection = new \ReflectionClass($className);
            $serviceId = $className;

            $this->discoverClassLevelAttributes(
                $reflection,
                $serviceId,
                $adminPages,
            );

            $this->discoverMethodLevelAttributes(
                $reflection,
                $serviceId,
                $actions,
                $filters,
                $shortcodes,
                $restRoutes,
                $crons,
                $ajaxHandlers,
                $assets,
            );

            if ($this->hasAnyAttribute($reflection)) {
                $container->autowire($serviceId, $className)
                    ->setAutoconfigured(true)
                    ->setPublic(false);

                $discoveredServiceIds[$serviceId] = new Reference($serviceId);
            }
        }

        $definition->addMethodCall('setActions', [$actions]);
        $definition->addMethodCall('setFilters', [$filters]);
        $definition->addMethodCall('setShortcodes', [$shortcodes]);
        $definition->addMethodCall('setRestRoutes', [$restRoutes]);
        $definition->addMethodCall('setCrons', [$crons]);
        $definition->addMethodCall('setPostTypes', [$postTypes]);
        $definition->addMethodCall('setTaxonomies', [$taxonomies]);
        $definition->addMethodCall('setAdminPages', [$adminPages]);
        $definition->addMethodCall('setAjaxHandlers', [$ajaxHandlers]);
        $definition->addMethodCall('setAssets', [$assets]);

        // Wire service locator into all registrars that need lazy service resolution
        $serviceLocator = ServiceLocatorTagPass::register($container, $discoveredServiceIds);

        $registrarsWithLocator = [
            HookRegistrar::class,
            RestRouteRegistrar::class,
            AdminPageRegistrar::class,
            CronRegistrar::class,
            AjaxRegistrar::class,
        ];

        foreach ($registrarsWithLocator as $registrarClass) {
            if ($container->hasDefinition($registrarClass)) {
                $container->getDefinition($registrarClass)
                    ->setArgument('$serviceLocator', $serviceLocator);
            }
        }
    }

    /**
     * @param \ReflectionClass<object>   $reflection
     * @param list<array<string, mixed>> &$adminPages
     */
    private function discoverClassLevelAttributes(
        \ReflectionClass $reflection,
        string $serviceId,
        array &$adminPages,
    ): void {
        foreach ($reflection->getAttributes(AdminPage::class) as $attr) {
            $page = $attr->newInstance();
            $adminPages[] = [
                'title' => $page->title,
                'menuSlug' => $page->menuSlug,
                'serviceId' => $serviceId,
                'capability' => $page->capability,
                'icon' => $page->icon,
                'position' => $page->position,
                'parentSlug' => $page->parentSlug,
            ];
        }
    }

    /**
     * @param list<array<string, mixed>> &$actions
     * @param list<array<string, mixed>> &$filters
     * @param list<array<string, mixed>> &$shortcodes
     * @param list<array<string, mixed>> &$restRoutes
     * @param list<array<string, mixed>> &$crons
     * @param list<array<string, mixed>> &$ajaxHandlers
     * @param list<array<string, mixed>> &$assets
     */
    private function discoverMethodLevelAttributes(
        \ReflectionClass $reflection,
        string $serviceId,
        array &$actions,
        array &$filters,
        array &$shortcodes,
        array &$restRoutes,
        array &$crons,
        array &$ajaxHandlers,
        array &$assets,
    ): void {
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($method->getAttributes(Action::class) as $attr) {
                $action = $attr->newInstance();
                $actions[] = [
                    'hook' => $action->hook,
                    'serviceId' => $serviceId,
                    'method' => $method->getName(),
                    'priority' => $action->priority,
                    'acceptedArgs' => $action->acceptedArgs,
                ];
            }

            foreach ($method->getAttributes(Filter::class) as $attr) {
                $filter = $attr->newInstance();
                $filters[] = [
                    'hook' => $filter->hook,
                    'serviceId' => $serviceId,
                    'method' => $method->getName(),
                    'priority' => $filter->priority,
                    'acceptedArgs' => $filter->acceptedArgs,
                ];
            }

            foreach ($method->getAttributes(Shortcode::class) as $attr) {
                $shortcode = $attr->newInstance();
                $shortcodes[] = [
                    'tag' => $shortcode->tag,
                    'serviceId' => $serviceId,
                    'method' => $method->getName(),
                ];
            }

            foreach ($method->getAttributes(RestRoute::class) as $attr) {
                $route = $attr->newInstance();
                $restRoutes[] = [
                    'namespace' => $route->namespace,
                    'route' => $route->route,
                    'methods' => $route->methods,
                    'serviceId' => $serviceId,
                    'method' => $method->getName(),
                    'permission' => $route->permission,
                ];
            }

            foreach ($method->getAttributes(CronJob::class) as $attr) {
                $cron = $attr->newInstance();
                $hook = $cron->hook ?? 'sympress_cron_'.strtolower($reflection->getShortName()).'_'.$method->getName();
                $crons[] = [
                    'hook' => $hook,
                    'schedule' => $cron->schedule,
                    'serviceId' => $serviceId,
                    'method' => $method->getName(),
                    'customInterval' => $cron->customInterval,
                ];
            }

            foreach ($method->getAttributes(AjaxHandler::class) as $attr) {
                $ajax = $attr->newInstance();
                $ajaxHandlers[] = [
                    'action' => $ajax->action,
                    'serviceId' => $serviceId,
                    'method' => $method->getName(),
                    'public' => $ajax->public,
                ];
            }

            foreach ($method->getAttributes(AssetEnqueue::class) as $attr) {
                $asset = $attr->newInstance();
                $assets[] = [
                    'hook' => $asset->hook,
                    'serviceId' => $serviceId,
                    'method' => $method->getName(),
                ];
            }
        }
    }

    private function hasAnyAttribute(\ReflectionClass $reflection): bool
    {
        $classAttributes = [AdminPage::class];

        foreach ($classAttributes as $attrClass) {
            if ($reflection->getAttributes($attrClass) !== []) {
                return true;
            }
        }

        $methodAttributes = [
            Action::class, Filter::class, Shortcode::class,
            RestRoute::class, CronJob::class, AjaxHandler::class,
            AssetEnqueue::class,
        ];

        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($methodAttributes as $attrClass) {
                if ($method->getAttributes($attrClass) !== []) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array<string> $paths
     *
     * @return list<class-string>
     */
    private function discoverClasses(array $paths): array
    {
        $classes = [];
        $existingPaths = array_filter($paths, static fn (string $p): bool => is_dir($p));

        if ($existingPaths === []) {
            return $classes;
        }

        $finder = new Finder();
        $finder->files()->name('*.php')->in($existingPaths);

        foreach ($finder as $file) {
            $className = $this->extractClassName($file->getRealPath());

            if ($className === null) {
                continue;
            }

            if (!class_exists($className)) {
                continue;
            }

            $classes[] = $className;
        }

        return $classes;
    }

    private function extractClassName(string $filePath): ?string
    {
        $contents = file_get_contents($filePath);

        if ($contents === false) {
            return null;
        }

        $tokens = \PhpToken::tokenize($contents);
        $namespace = null;
        $class = null;

        for ($i = 0, $count = \count($tokens); $i < $count; ++$i) {
            if ($tokens[$i]->id === \T_NAMESPACE) {
                $namespaceParts = [];
                ++$i;

                while ($i < $count && $tokens[$i]->text !== ';') {
                    if ($tokens[$i]->id === \T_NAME_QUALIFIED || $tokens[$i]->id === \T_STRING) {
                        $namespaceParts[] = $tokens[$i]->text;
                    }
                    ++$i;
                }

                $namespace = implode('\\', $namespaceParts);
            }

            if ($tokens[$i]->id === \T_CLASS && $class === null) {
                ++$i;

                while ($i < $count && $tokens[$i]->id === \T_WHITESPACE) {
                    ++$i;
                }

                if ($i < $count && $tokens[$i]->id === \T_STRING) {
                    $class = $tokens[$i]->text;
                }
            }
        }

        if ($namespace === null || $class === null) {
            return null;
        }

        return $namespace.'\\'.$class;
    }
}
