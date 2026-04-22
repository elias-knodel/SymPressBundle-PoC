# WordPress Framework Bundle

An attribute-driven WordPress framework bundle for Symfony. Register hooks, REST routes, custom post types, cron jobs, admin pages, and more using PHP 8.4 attributes -- no manual `add_action()` / `add_filter()` boilerplate needed.

## Installation

```bash
composer require sympress/bundle
```

Register the bundle in your kernel:

```php
return [
    // ...
    SymPress\Bundle\SymPressBundle::class => ['all' => true],
];
```

## How It Works

The bundle scans your service classes for PHP attributes at compile time. Each attribute maps to a WordPress registration concept (action hooks, filters, REST routes, etc.). A set of compiler passes discovers these attributes and wires everything into the Symfony container, so when WordPress boots, all hooks, routes, and registrations are active automatically.

**The flow:**

1. You annotate your service methods/classes with attributes like `#[Action]`, `#[Filter]`, `#[RestRoute]`, etc.
2. The bundle's compiler passes discover the attributes and build metadata.
3. Registrar services (`HookRegistrar`, `RestRouteRegistrar`, `CronRegistrar`, etc.) register everything with WordPress at the appropriate lifecycle point.

No manual `functions.php` wiring is required.

---

## Attributes Reference

### `#[Action]` -- WordPress Action Hooks

Registers a method or class as a callback for a WordPress action hook via `add_action()`.

**Target:** Method or Class

| Parameter      | Type     | Default | Description                                      |
|----------------|----------|---------|--------------------------------------------------|
| `hook`         | `string` | *(required)* | The WordPress action hook name              |
| `priority`     | `int`    | `10`    | Execution priority (lower = earlier)             |
| `acceptedArgs` | `int`    | `1`     | Number of arguments the callback accepts         |

**Examples:**

```php
use SymPress\Bundle\Attribute\Action;

class MyPlugin
{
    #[Action(hook: 'init')]
    public function onInit(): void
    {
        // Runs during WordPress init.
    }

    #[Action(hook: 'wp_footer', priority: 20)]
    public function renderFooter(): void
    {
        echo '<p>Custom footer</p>';
    }
}

// Or on a class (uses __invoke):
#[Action(hook: 'admin_init', priority: 5, acceptedArgs: 0)]
final class AdminInitHandler
{
    public function __invoke(): void
    {
        // Runs early during admin_init.
    }
}
```

---

### `#[Filter]` -- WordPress Filter Hooks

Registers a method or class as a callback for a WordPress filter hook via `add_filter()`. The method **must return** the filtered value.

**Target:** Method or Class

| Parameter      | Type     | Default | Description                                      |
|----------------|----------|---------|--------------------------------------------------|
| `hook`         | `string` | *(required)* | The WordPress filter hook name              |
| `priority`     | `int`    | `10`    | Execution priority (lower = earlier)             |
| `acceptedArgs` | `int`    | `1`     | Number of arguments the callback accepts         |

**Examples:**

```php
use SymPress\Bundle\Attribute\Filter;

class ContentModifier
{
    #[Filter(hook: 'the_content')]
    public function appendDisclaimer(string $content): string
    {
        return $content . '<p class="disclaimer">Disclaimer text.</p>';
    }

    #[Filter(hook: 'the_title', priority: 20)]
    public function uppercaseTitle(string $title): string
    {
        return strtoupper($title);
    }
}

// On a class:
#[Filter(hook: 'wp_mail', acceptedArgs: 1)]
final class MailFilter
{
    public function __invoke(array $args): array
    {
        $args['headers'][] = 'X-Custom-Header: value';
        return $args;
    }
}
```

---

### `#[Shortcode]` -- WordPress Shortcodes

Registers a method as a shortcode handler via `add_shortcode()`. The method should accept attributes and optional content, and return the rendered HTML string.

**Target:** Method only

| Parameter | Type     | Default | Description                        |
|-----------|----------|---------|------------------------------------|
| `tag`     | `string` | *(required)* | The shortcode tag name        |

**Example:**

```php
use SymPress\Bundle\Attribute\Shortcode;

class MyShortcodes
{
    #[Shortcode(tag: 'greeting')]
    public function renderGreeting(array $atts, string $content = ''): string
    {
        $name = $atts['name'] ?? 'World';
        return sprintf('<p>Hello, %s! %s</p>', esc_html($name), esc_html($content));
    }

    #[Shortcode(tag: 'current_year')]
    public function renderCurrentYear(): string
    {
        return date('Y');
    }
}
```

Usage in post content: `[greeting name="Alice"]Welcome![/greeting]`

---

### `#[RestRoute]` -- WordPress REST API Routes

Registers a method as a REST API endpoint via `register_rest_route()`. The method receives a `WP_REST_Request` and should return response data.

**Target:** Method only

| Parameter    | Type       | Default     | Description                                |
|--------------|------------|-------------|--------------------------------------------|
| `route`      | `string`   | *(required)* | Route pattern (e.g. `/users/(?P<id>\d+)`) |
| `namespace`  | `string`   | `'app/v1'`  | REST API namespace                         |
| `methods`    | `string[]` | `['GET']`   | HTTP methods this route responds to        |
| `permission` | `?string`  | `null`      | Required capability, or `null` for public  |

**Examples:**

```php
use SymPress\Bundle\Attribute\RestRoute;

class UserEndpoints
{
    #[RestRoute(
        route: '/users',
        namespace: 'myapp/v1',
        methods: ['GET'],
    )]
    public function listUsers(\WP_REST_Request $request): array
    {
        return ['users' => get_users()];
    }

    #[RestRoute(
        route: '/users/(?P<id>\d+)',
        namespace: 'myapp/v1',
        methods: ['GET', 'PATCH'],
        permission: 'edit_users',
    )]
    public function getOrUpdateUser(\WP_REST_Request $request): array
    {
        $id = (int) $request->get_param('id');
        return ['user' => get_userdata($id)];
    }

    // Public endpoint (no auth required):
    #[RestRoute(route: '/public/health', methods: ['GET'])]
    public function healthCheck(): array
    {
        return ['status' => 'ok'];
    }
}
```

---

### `#[CronJob]` -- WordPress Cron Jobs

Registers a method as a scheduled cron job via `wp_schedule_event()`. Hook names are auto-generated if not specified. Custom intervals are automatically registered via the `cron_schedules` filter.

**Target:** Method only

| Parameter        | Type     | Default    | Description                                          |
|------------------|----------|------------|------------------------------------------------------|
| `schedule`       | `string` | `'hourly'` | Recurrence name (`hourly`, `daily`, `twicedaily`, `weekly`, or custom) |
| `hook`           | `?string`| `null`     | Action hook name (auto-generated if null)            |
| `customInterval` | `?int`   | `null`     | Interval in seconds for custom schedules             |

**Built-in schedules:** `hourly` (3600s), `twicedaily` (43200s), `daily` (86400s), `weekly` (604800s).

**Examples:**

```php
use SymPress\Bundle\Attribute\CronJob;

class MaintenanceTasks
{
    #[CronJob(schedule: 'daily')]
    public function cleanExpiredTokens(): void
    {
        // Runs once daily. Hook name is auto-generated.
    }

    #[CronJob(schedule: 'hourly', hook: 'myapp_sync_inventory')]
    public function syncInventory(): void
    {
        // Runs hourly with an explicit hook name.
    }

    #[CronJob(schedule: 'every_five_minutes', customInterval: 300)]
    public function checkExternalApi(): void
    {
        // Custom schedule registered automatically.
    }
}
```

---

### `#[AdminPage]` -- Admin Menu Pages

Registers a class as an admin page. When `parentSlug` is `null`, creates a top-level menu item via `add_menu_page()`. When set, creates a submenu item via `add_submenu_page()`. The class's `__invoke()` method renders the page.

**Target:** Class only

| Parameter    | Type      | Default                     | Description                          |
|--------------|-----------|-----------------------------|--------------------------------------|
| `title`      | `string`  | *(required)*                | Page title (browser tab + heading)   |
| `menuSlug`   | `string`  | *(required)*                | Unique slug for this page            |
| `capability` | `string`  | `'manage_options'`          | Required capability to access        |
| `icon`       | `string`  | `'dashicons-admin-generic'` | Dashicon or image URL                |
| `position`   | `int`     | `99`                        | Admin menu position                  |
| `parentSlug` | `?string` | `null`                      | Parent page slug (null = top-level)  |

**Examples:**

```php
use SymPress\Bundle\Attribute\AdminPage;

// Top-level menu page:
#[AdminPage(
    title: 'My Plugin Settings',
    menuSlug: 'my-plugin-settings',
    icon: 'dashicons-admin-settings',
    position: 80,
)]
final class SettingsPage
{
    public function __invoke(): void
    {
        echo '<div class="wrap"><h1>Settings</h1></div>';
    }
}

// Submenu page:
#[AdminPage(
    title: 'Advanced Options',
    menuSlug: 'my-plugin-advanced',
    parentSlug: 'my-plugin-settings',
    capability: 'manage_options',
)]
final class AdvancedOptionsPage
{
    public function __invoke(): void
    {
        echo '<div class="wrap"><h1>Advanced Options</h1></div>';
    }
}
```

---

### `#[AjaxHandler]` -- WordPress AJAX Endpoints

Registers a method as a WordPress AJAX handler via `wp_ajax_{action}` hooks. By default only available to logged-in users. Set `public: true` to also register the `wp_ajax_nopriv_{action}` hook.

**Target:** Method only

| Parameter | Type     | Default | Description                                        |
|-----------|----------|---------|----------------------------------------------------|
| `action`  | `string` | *(required)* | AJAX action name                              |
| `public`  | `bool`   | `false` | Also register for unauthenticated users            |

**Example:**

```php
use SymPress\Bundle\Attribute\AjaxHandler;

class AjaxEndpoints
{
    #[AjaxHandler(action: 'load_more_posts', public: true)]
    public function loadMorePosts(): void
    {
        $page = (int) ($_POST['page'] ?? 1);
        $posts = get_posts(['paged' => $page, 'posts_per_page' => 10]);
        wp_send_json_success(['posts' => $posts]);
    }

    #[AjaxHandler(action: 'save_user_preferences')]
    public function saveUserPreferences(): void
    {
        check_ajax_referer('save_prefs_nonce');
        update_user_meta(get_current_user_id(), 'preferences', $_POST['prefs']);
        wp_send_json_success();
    }
}
```

---

### `#[AssetEnqueue]` -- Script & Style Enqueuing

Marks a method as an asset enqueuer. The method is called during the specified WordPress enqueue hook and should contain `wp_enqueue_script()` / `wp_enqueue_style()` calls.

**Target:** Method only

| Parameter | Type     | Default                  | Description                                     |
|-----------|----------|--------------------------|-------------------------------------------------|
| `hook`    | `string` | `'wp_enqueue_scripts'`   | Enqueue hook: `wp_enqueue_scripts` (frontend), `admin_enqueue_scripts` (admin), or `login_enqueue_scripts` (login) |

**Example:**

```php
use SymPress\Bundle\Attribute\AssetEnqueue;

class AssetManager
{
    #[AssetEnqueue]
    public function enqueueFrontendAssets(): void
    {
        wp_enqueue_style('my-theme', get_stylesheet_uri(), [], '1.0.0');
        wp_enqueue_script('my-app', plugins_url('dist/app.js', __DIR__), ['jquery'], '1.0.0', true);
    }

    #[AssetEnqueue(hook: 'admin_enqueue_scripts')]
    public function enqueueAdminAssets(): void
    {
        wp_enqueue_style('my-admin', plugins_url('dist/admin.css', __DIR__));
    }

    #[AssetEnqueue(hook: 'login_enqueue_scripts')]
    public function enqueueLoginAssets(): void
    {
        wp_enqueue_style('my-login', plugins_url('dist/login.css', __DIR__));
    }
}
```

---

## Enums

The bundle provides several enums for type-safe configuration:

- **`HttpMethod`** -- `GET`, `POST`, `PUT`, `PATCH`, `DELETE` with `isReadOnly()` and `hasRequestBody()` helpers
- **`CronSchedule`** -- `HOURLY`, `TWICE_DAILY`, `DAILY`, `WEEKLY` with `intervalInSeconds()` and `displayName()` helpers
- **`HookTiming`** -- WordPress lifecycle points (`INIT`, `ADMIN_INIT`, `REST_API_INIT`, etc.) with `defaultPriority()` and `isAdminOnly()` helpers
