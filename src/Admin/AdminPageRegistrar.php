<?php

declare(strict_types=1);

namespace SymPress\Bundle\Admin;

use Psr\Container\ContainerInterface;
use SymPress\Bundle\Hook\HookRegistry;

/**
 * Registers admin menu pages with WordPress on `admin_menu`.
 */
final readonly class AdminPageRegistrar
{
    public function __construct(
        private HookRegistry $registry,
        private ContainerInterface $serviceLocator,
    ) {
    }

    public function register(): void
    {
        foreach ($this->registry->getAdminPages() as $page) {
            $callback = function () use ($page): void {
                $service = $this->serviceLocator->get($page->serviceId);
                \assert(is_object($service));

                if (method_exists($service, 'render')) {
                    $service->render();
                } elseif (method_exists($service, '__invoke')) {
                    $service();
                }
            };

            if ($page->isSubmenu()) {
                add_submenu_page(
                    $page->parentSlug ?? '',
                    $page->title,
                    $page->title,
                    $page->capability,
                    $page->menuSlug,
                    $callback,
                );
            } else {
                add_menu_page(
                    $page->title,
                    $page->title,
                    $page->capability,
                    $page->menuSlug,
                    $callback,
                    $page->icon,
                    $page->position,
                );
            }
        }
    }
}
