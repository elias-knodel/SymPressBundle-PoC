<?php

declare(strict_types=1);

namespace SymPress\Bundle\Hook;

use Psr\Container\ContainerInterface;

/**
 * Registers all discovered hooks with WordPress at runtime.
 *
 * Uses a service locator to lazily resolve service instances only when
 * WordPress actually fires the hook. This ensures minimal overhead.
 */
final readonly class HookRegistrar
{
    public function __construct(
        private HookRegistry $registry,
        private ContainerInterface $serviceLocator,
    ) {
    }

    /**
     * Register all discovered actions, filters, shortcodes and asset enqueues.
     *
     * Should be called as early as possible (plugins_loaded or earlier).
     */
    public function registerHooks(): void
    {
        $this->registerActions();
        $this->registerFilters();
        $this->registerShortcodes();
        $this->registerAssets();
    }

    private function registerActions(): void
    {
        foreach ($this->registry->getActions() as $action) {
            add_action(
                $action->hook,
                $this->createVoidCallback($action->serviceId, $action->method),
                $action->priority,
                $action->acceptedArgs,
            );
        }
    }

    private function registerFilters(): void
    {
        foreach ($this->registry->getFilters() as $filter) {
            add_filter(
                $filter->hook,
                $this->createFilterCallback($filter->serviceId, $filter->method),
                $filter->priority,
                $filter->acceptedArgs,
            );
        }
    }

    private function registerShortcodes(): void
    {
        foreach ($this->registry->getShortcodes() as $shortcode) {
            \assert($shortcode->tag !== '');
            add_shortcode(
                $shortcode->tag,
                $this->createShortcodeCallback($shortcode->serviceId, $shortcode->method),
            );
        }
    }

    private function registerAssets(): void
    {
        foreach ($this->registry->getAssets() as $asset) {
            add_action(
                $asset->hook,
                $this->createVoidCallback($asset->serviceId, $asset->method),
            );
        }
    }

    /**
     * @return callable(): void
     */
    private function createVoidCallback(string $serviceId, string $method): callable
    {
        return function () use ($serviceId, $method): void {
            $service = $this->serviceLocator->get($serviceId);

            $service->{$method}(...\func_get_args());
        };
    }

    /**
     * @return callable(mixed...): mixed
     */
    private function createFilterCallback(string $serviceId, string $method): callable
    {
        return function () use ($serviceId, $method) {
            $service = $this->serviceLocator->get($serviceId);

            return $service->{$method}(...\func_get_args());
        };
    }

    /**
     * @return callable(array<int|string, mixed>, string|null, string): string
     */
    private function createShortcodeCallback(string $serviceId, string $method): callable
    {
        return function () use ($serviceId, $method): string {
            $service = $this->serviceLocator->get($serviceId);
            \assert(is_object($service));

            /** @var string|\Stringable|int|float|bool|null $result */
            $result = $service->{$method}(...\func_get_args());

            return (string) $result;
        };
    }
}
