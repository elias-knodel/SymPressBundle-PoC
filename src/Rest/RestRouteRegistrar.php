<?php

declare(strict_types=1);

namespace SymPress\Bundle\Rest;

use Psr\Container\ContainerInterface;
use SymPress\Bundle\Hook\HookRegistry;

/**
 * Registers REST API routes with WordPress on `rest_api_init`.
 */
final readonly class RestRouteRegistrar
{
    public function __construct(
        private HookRegistry $registry,
        private ContainerInterface $serviceLocator,
    ) {
    }

    public function register(): void
    {
        foreach ($this->registry->getRestRoutes() as $route) {
            register_rest_route(
                $route->namespace,
                $route->route,
                [
                    'methods' => $route->methods,
                    'callback' => function (\WP_REST_Request $request) use ($route): mixed {
                        $service = $this->serviceLocator->get($route->serviceId);

                        return $service->{$route->method}($request);
                    },
                    'permission_callback' => $this->buildPermissionCallback($route->permission),
                ],
            );
        }
    }

    private function buildPermissionCallback(?string $permission): callable
    {
        if ($permission === null) {
            return static fn (): bool => true;
        }

        return static fn (): bool => current_user_can($permission);
    }
}
