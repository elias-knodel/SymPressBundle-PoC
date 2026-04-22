<?php

declare(strict_types=1);

namespace SymPress\Bundle\Attribute;

/**
 * Marks a method as an asset enqueuer for WordPress scripts and styles.
 *
 * The annotated method is called during the specified enqueue hook. The method
 * should contain the actual wp_enqueue_script() and wp_enqueue_style() calls,
 * giving full control over asset registration and configuration.
 *
 * Example usage:
 *
 *     class AssetManager
 *     {
 *         #[AssetEnqueue]
 *         public function enqueueFrontendAssets(): void
 *         {
 *             wp_enqueue_style('my-theme', get_stylesheet_uri(), [], '1.0.0');
 *             wp_enqueue_script('my-app', plugins_url('dist/app.js', __DIR__), ['jquery'], '1.0.0', true);
 *         }
 *
 *         #[AssetEnqueue(hook: 'admin_enqueue_scripts')]
 *         public function enqueueAdminAssets(): void
 *         {
 *             wp_enqueue_style('my-admin', plugins_url('dist/admin.css', __DIR__));
 *             wp_enqueue_script('my-admin-js', plugins_url('dist/admin.js', __DIR__), [], '1.0.0', true);
 *         }
 *
 *         #[AssetEnqueue(hook: 'login_enqueue_scripts')]
 *         public function enqueueLoginAssets(): void
 *         {
 *             wp_enqueue_style('my-login', plugins_url('dist/login.css', __DIR__));
 *         }
 *     }
 *
 * @see https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 * @see https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final readonly class AssetEnqueue
{
    /**
     * @param string $hook The WordPress action hook on which to enqueue assets.
     *                     Common values: 'wp_enqueue_scripts' (frontend),
     *                     'admin_enqueue_scripts' (admin), 'login_enqueue_scripts' (login).
     *                     Default 'wp_enqueue_scripts'.
     */
    public function __construct(
        public string $hook = 'wp_enqueue_scripts',
    ) {
    }
}
