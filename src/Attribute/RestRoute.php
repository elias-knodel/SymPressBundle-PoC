<?php

declare(strict_types=1);

namespace SymPress\Bundle\Attribute;

/**
 * Registers a method as a WordPress REST API route via register_rest_route().
 *
 * The annotated method will handle incoming REST API requests matching the
 * specified route pattern. The method receives a WP_REST_Request instance
 * and should return a WP_REST_Response or data array.
 *
 * Example usage:
 *
 *     class UserEndpoints
 *     {
 *         #[RestRoute(
 *             route: '/users',
 *             namespace: 'myapp/v1',
 *             methods: ['GET'],
 *         )]
 *         public function listUsers(\WP_REST_Request $request): array
 *         {
 *             return ['users' => get_users()];
 *         }
 *
 *         #[RestRoute(
 *             route: '/users/(?P<id>\d+)',
 *             namespace: 'myapp/v1',
 *             methods: ['GET', 'PATCH'],
 *             permission: 'edit_users',
 *         )]
 *         public function getOrUpdateUser(\WP_REST_Request $request): array
 *         {
 *             $id = (int) $request->get_param('id');
 *             return ['user' => get_userdata($id)];
 *         }
 *
 *         #[RestRoute(route: '/public/health', methods: ['GET'])]
 *         public function healthCheck(): array
 *         {
 *             return ['status' => 'ok'];
 *         }
 *     }
 *
 * @see https://developer.wordpress.org/reference/functions/register_rest_route/
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final readonly class RestRoute
{
    /**
     * @param string        $route      The REST route pattern (e.g., '/users/(?P<id>\d+)').
     * @param string        $namespace  The REST API namespace. Default 'app/v1'.
     * @param array<string> $methods    HTTP methods this route responds to. Default ['GET'].
     * @param string|null   $permission The capability required to access this route,
     *                                  or null for public access. Default null.
     */
    public function __construct(
        public string $route,
        public string $namespace = 'app/v1',
        public array $methods = ['GET'],
        public ?string $permission = null,
    ) {
    }
}
