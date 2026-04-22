<?php

declare(strict_types=1);

namespace SymPress\Bundle\Attribute;

/**
 * Registers a method or class as a WordPress action hook via add_action().
 *
 * When applied to a method, that method will be called when the specified
 * WordPress action hook fires. When applied to a class, the class's __invoke
 * method will be used as the callback.
 *
 * Example usage on a method:
 *
 *     class MyPlugin
 *     {
 *         #[Action(hook: 'init')]
 *         public function onInit(): void
 *         {
 *             // Runs during WordPress init action.
 *         }
 *
 *         #[Action(hook: 'wp_footer', priority: 20)]
 *         public function renderFooter(): void
 *         {
 *             echo '<p>Custom footer content</p>';
 *         }
 *     }
 *
 * Example usage on a class:
 *
 *     #[Action(hook: 'admin_init', priority: 5, acceptedArgs: 0)]
 *     final class AdminInitHandler
 *     {
 *         public function __invoke(): void
 *         {
 *             // Runs early during admin_init.
 *         }
 *     }
 *
 * @see https://developer.wordpress.org/reference/functions/add_action/
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS)]
final readonly class Action
{
    /**
     * @param string $hook         the name of the WordPress action hook
     * @param int    $priority     The priority at which the callback is executed.
     *                             Lower numbers correspond to earlier execution. Default 10.
     * @param int    $acceptedArgs The number of arguments the callback accepts. Default 1.
     */
    public function __construct(
        public string $hook,
        public int $priority = 10,
        public int $acceptedArgs = 1,
    ) {
    }
}
