<?php

declare(strict_types=1);

namespace SymPress\Bundle\Attribute;

/**
 * Registers a method as a WordPress AJAX handler via wp_ajax_{action} hooks.
 *
 * The annotated method handles AJAX requests for the specified action name.
 * By default, the handler is only available to logged-in users. Set public
 * to true to also register the wp_ajax_nopriv_{action} hook for
 * unauthenticated users.
 *
 * Example usage:
 *
 *     class AjaxEndpoints
 *     {
 *         #[AjaxHandler(action: 'load_more_posts', public: true)]
 *         public function loadMorePosts(): void
 *         {
 *             $page = (int) ($_POST['page'] ?? 1);
 *             $posts = get_posts(['paged' => $page, 'posts_per_page' => 10]);
 *             wp_send_json_success(['posts' => $posts]);
 *         }
 *
 *         #[AjaxHandler(action: 'save_user_preferences')]
 *         public function saveUserPreferences(): void
 *         {
 *             check_ajax_referer('save_prefs_nonce');
 *             // Only available to logged-in users.
 *             update_user_meta(get_current_user_id(), 'preferences', $_POST['prefs']);
 *             wp_send_json_success();
 *         }
 *     }
 *
 * @see https://developer.wordpress.org/plugins/javascript/ajax/
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final readonly class AjaxHandler
{
    /**
     * @param string $action the AJAX action name (used in the wp_ajax_{action} hook)
     * @param bool   $public Whether to also register the nopriv hook for
     *                       unauthenticated users. Default false.
     */
    public function __construct(
        public string $action,
        public bool $public = false,
    ) {
    }
}
