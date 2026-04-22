<?php

declare(strict_types=1);

namespace SymPress\Bundle\Attribute;

/**
 * Registers a method or class as a WordPress filter hook via add_filter().
 *
 * When applied to a method, that method will be called when the specified
 * WordPress filter hook fires. The method must return the filtered value.
 * When applied to a class, the class's __invoke method will be used as the callback.
 *
 * Example usage on a method:
 *
 *     class ContentModifier
 *     {
 *         #[Filter(hook: 'the_content')]
 *         public function appendDisclaimer(string $content): string
 *         {
 *             return $content . '<p class="disclaimer">Disclaimer text.</p>';
 *         }
 *
 *         #[Filter(hook: 'the_title', priority: 20)]
 *         public function uppercaseTitle(string $title): string
 *         {
 *             return strtoupper($title);
 *         }
 *     }
 *
 * Example usage on a class:
 *
 *     #[Filter(hook: 'wp_mail', acceptedArgs: 1)]
 *     final class MailFilter
 *     {
 *         public function __invoke(array $args): array
 *         {
 *             $args['headers'][] = 'X-Custom-Header: value';
 *             return $args;
 *         }
 *     }
 *
 * @see https://developer.wordpress.org/reference/functions/add_filter/
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS)]
final readonly class Filter
{
    /**
     * @param string $hook         the name of the WordPress filter hook
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
