<?php

declare(strict_types=1);

namespace SymPress\Bundle\Ajax;

use Psr\Container\ContainerInterface;
use SymPress\Bundle\Hook\HookRegistry;

/**
 * Registers AJAX action handlers with WordPress.
 */
final readonly class AjaxRegistrar
{
    public function __construct(
        private HookRegistry $registry,
        private ContainerInterface $serviceLocator,
    ) {
    }

    public function register(): void
    {
        foreach ($this->registry->getAjaxHandlers() as $ajax) {
            $callback = function () use ($ajax): void {
                $service = $this->serviceLocator->get($ajax->serviceId);

                $service->{$ajax->method}(...\func_get_args());
            };

            add_action('wp_ajax_'.$ajax->action, $callback);

            if ($ajax->public) {
                add_action('wp_ajax_nopriv_'.$ajax->action, $callback);
            }
        }
    }
}
