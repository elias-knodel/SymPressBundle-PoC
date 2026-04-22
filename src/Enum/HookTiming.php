<?php

declare(strict_types=1);

namespace SymPress\Bundle\Enum;

/**
 * WordPress hook timing points for registration scheduling.
 *
 * Determines when hooks, post types, taxonomies, and other registrations
 * are executed in the WordPress lifecycle.
 */
enum HookTiming: string
{
    case MUPLUGINS_LOADED = 'muplugins_loaded';
    case PLUGINS_LOADED = 'plugins_loaded';
    case AFTER_SETUP_THEME = 'after_setup_theme';
    case INIT = 'init';
    case ADMIN_INIT = 'admin_init';
    case ADMIN_MENU = 'admin_menu';
    case REST_API_INIT = 'rest_api_init';
    case WP_LOADED = 'wp_loaded';
    case WP_ENQUEUE_SCRIPTS = 'wp_enqueue_scripts';
    case ADMIN_ENQUEUE_SCRIPTS = 'admin_enqueue_scripts';
    case IMMEDIATE = 'immediate';

    /**
     * Default priority for this timing point in WordPress's execution order.
     */
    public function defaultPriority(): int
    {
        return 10;
    }

    /**
     * Whether this timing runs only in the admin context.
     */
    public function isAdminOnly(): bool
    {
        return match ($this) {
            self::ADMIN_INIT, self::ADMIN_MENU, self::ADMIN_ENQUEUE_SCRIPTS => true,
            default => false,
        };
    }
}
