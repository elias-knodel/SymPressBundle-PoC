<?php

declare(strict_types=1);

namespace SymPress\Bundle\Attribute;

/**
 * Registers a method as a WordPress shortcode handler via add_shortcode().
 *
 * The annotated method will be called whenever the shortcode is encountered
 * in post content. The method should accept an array of attributes and an
 * optional content string, and return the shortcode output as a string.
 *
 * Example usage:
 *
 *     class MyShortcodes
 *     {
 *         #[Shortcode(tag: 'greeting')]
 *         public function renderGreeting(array $atts, string $content = ''): string
 *         {
 *             $name = $atts['name'] ?? 'World';
 *             return sprintf('<p>Hello, %s! %s</p>', esc_html($name), esc_html($content));
 *         }
 *
 *         #[Shortcode(tag: 'current_year')]
 *         public function renderCurrentYear(): string
 *         {
 *             return date('Y');
 *         }
 *     }
 *
 *     // In post content: [greeting name="Alice"]Welcome![/greeting]
 *     // Output: <p>Hello, Alice! Welcome!</p>
 *
 * @see https://developer.wordpress.org/reference/functions/add_shortcode/
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final readonly class Shortcode
{
    /**
     * @param string $tag The shortcode tag to register (e.g., 'greeting').
     */
    public function __construct(
        public string $tag,
    ) {
    }
}
