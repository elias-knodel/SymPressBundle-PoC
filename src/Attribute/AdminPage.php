<?php

declare(strict_types=1);

namespace SymPress\Bundle\Attribute;

/**
 * Registers a class as a WordPress admin page via add_menu_page() or add_submenu_page().
 *
 * When parentSlug is null, the page is registered as a top-level menu item.
 * When parentSlug is provided, the page is registered as a submenu item under
 * the specified parent.
 *
 * Example usage as a top-level menu page:
 *
 *     #[AdminPage(
 *         title: 'My Plugin Settings',
 *         menuSlug: 'my-plugin-settings',
 *         icon: 'dashicons-admin-settings',
 *         position: 80,
 *     )]
 *     final class SettingsPage
 *     {
 *         public function __invoke(): void
 *         {
 *             echo '<div class="wrap"><h1>Settings</h1></div>';
 *         }
 *     }
 *
 * Example usage as a submenu page:
 *
 *     #[AdminPage(
 *         title: 'Advanced Options',
 *         menuSlug: 'my-plugin-advanced',
 *         parentSlug: 'my-plugin-settings',
 *         capability: 'manage_options',
 *     )]
 *     final class AdvancedOptionsPage
 *     {
 *         public function __invoke(): void
 *         {
 *             echo '<div class="wrap"><h1>Advanced Options</h1></div>';
 *         }
 *     }
 *
 * @see https://developer.wordpress.org/reference/functions/add_menu_page/
 * @see https://developer.wordpress.org/reference/functions/add_submenu_page/
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class AdminPage
{
    /**
     * @param string      $title      the page title displayed in the browser tab and page heading
     * @param string      $menuSlug   the unique slug for this menu page
     * @param string      $capability The capability required to access this page. Default 'manage_options'.
     * @param string      $icon       The dashicon or URL for the menu icon. Default 'dashicons-admin-generic'.
     * @param int         $position   The position in the admin menu. Default 99.
     * @param string|null $parentSlug The slug of the parent menu page. If set, the page is
     *                                registered as a submenu item. Default null (top-level).
     */
    public function __construct(
        public string $title,
        public string $menuSlug,
        public string $capability = 'manage_options',
        public string $icon = 'dashicons-admin-generic',
        public int $position = 99,
        public ?string $parentSlug = null,
    ) {
    }
}
