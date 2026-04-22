<?php

declare(strict_types=1);

namespace SymPress\Bundle\Hook;

use SymPress\Bundle\Hook\Metadata\ActionMetadata;
use SymPress\Bundle\Hook\Metadata\AdminPageMetadata;
use SymPress\Bundle\Hook\Metadata\AjaxMetadata;
use SymPress\Bundle\Hook\Metadata\AssetMetadata;
use SymPress\Bundle\Hook\Metadata\CronMetadata;
use SymPress\Bundle\Hook\Metadata\FilterMetadata;
use SymPress\Bundle\Hook\Metadata\RestRouteMetadata;
use SymPress\Bundle\Hook\Metadata\ShortcodeMetadata;

/**
 * Central registry for all discovered hook metadata.
 *
 * Populated at container compile time by compiler passes, then read
 * at runtime by registrars to wire everything into WordPress.
 */
class HookRegistry
{
    /** @var list<ActionMetadata> */
    private array $actions = [];

    /** @var list<FilterMetadata> */
    private array $filters = [];

    /** @var list<ShortcodeMetadata> */
    private array $shortcodes = [];

    /** @var list<RestRouteMetadata> */
    private array $restRoutes = [];

    /** @var list<CronMetadata> */
    private array $crons = [];

    /** @var list<AdminPageMetadata> */
    private array $adminPages = [];

    /** @var list<AjaxMetadata> */
    private array $ajaxHandlers = [];

    /** @var list<AssetMetadata> */
    private array $assets = [];

    /**
     * @param list<array{hook: string, serviceId: string, method: string, priority?: int, acceptedArgs?: int}> $actions
     */
    public function setActions(array $actions): void
    {
        $this->actions = array_map(static fn (array $a): ActionMetadata => new ActionMetadata(...$a), $actions);
    }

    /**
     * @return list<ActionMetadata>
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param list<array{hook: string, serviceId: string, method: string, priority?: int, acceptedArgs?: int}> $filters
     */
    public function setFilters(array $filters): void
    {
        $this->filters = array_map(static fn (array $a): FilterMetadata => new FilterMetadata(...$a), $filters);
    }

    /**
     * @return list<FilterMetadata>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param list<array{tag: string, serviceId: string, method: string}> $shortcodes
     */
    public function setShortcodes(array $shortcodes): void
    {
        $this->shortcodes = array_map(static fn (array $a): ShortcodeMetadata => new ShortcodeMetadata(...$a), $shortcodes);
    }

    /**
     * @return list<ShortcodeMetadata>
     */
    public function getShortcodes(): array
    {
        return $this->shortcodes;
    }

    /**
     * @param list<array{namespace: string, route: string, methods: list<string>, serviceId: string, method: string, permission?: string|null}> $restRoutes
     */
    public function setRestRoutes(array $restRoutes): void
    {
        $this->restRoutes = array_map(static fn (array $a): RestRouteMetadata => new RestRouteMetadata(...$a), $restRoutes);
    }

    /**
     * @return list<RestRouteMetadata>
     */
    public function getRestRoutes(): array
    {
        return $this->restRoutes;
    }

    /**
     * @param list<array{hook: string, schedule: string, serviceId: string, method: string, customInterval?: int|null, customDisplayName?: string|null}> $crons
     */
    public function setCrons(array $crons): void
    {
        $this->crons = array_map(static fn (array $a): CronMetadata => new CronMetadata(...$a), $crons);
    }

    /**
     * @return list<CronMetadata>
     */
    public function getCrons(): array
    {
        return $this->crons;
    }

    /**
     * @param list<array{title: string, menuSlug: string, serviceId: string, capability?: string, icon?: string, position?: int, parentSlug?: string|null}> $adminPages
     */
    public function setAdminPages(array $adminPages): void
    {
        $this->adminPages = array_map(static fn (array $a): AdminPageMetadata => new AdminPageMetadata(...$a), $adminPages);
    }

    /**
     * @return list<AdminPageMetadata>
     */
    public function getAdminPages(): array
    {
        return $this->adminPages;
    }

    /**
     * @param list<array{action: string, serviceId: string, method: string, public?: bool}> $ajaxHandlers
     */
    public function setAjaxHandlers(array $ajaxHandlers): void
    {
        $this->ajaxHandlers = array_map(static fn (array $a): AjaxMetadata => new AjaxMetadata(...$a), $ajaxHandlers);
    }

    /**
     * @return list<AjaxMetadata>
     */
    public function getAjaxHandlers(): array
    {
        return $this->ajaxHandlers;
    }

    /**
     * @param list<array{hook: string, serviceId: string, method: string}> $assets
     */
    public function setAssets(array $assets): void
    {
        $this->assets = array_map(static fn (array $a): AssetMetadata => new AssetMetadata(...$a), $assets);
    }

    /**
     * @return list<AssetMetadata>
     */
    public function getAssets(): array
    {
        return $this->assets;
    }
}
