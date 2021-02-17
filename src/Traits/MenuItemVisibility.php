<?php

declare(strict_types=1);

namespace Contributte\MenuControl\Traits;

use Contributte\MenuControl\Config\MenuVisibility;

trait MenuItemVisibility
{

	/**
	 * @var MenuVisibility
	 */
	private $visibility;

	public function isVisibleOnMenu(): bool
	{
		return $this->visibility->menu;
	}

	public function setMenuVisibility(bool $visibility): void
	{
		$this->visibility->menu = $visibility;
	}

	public function isVisibleOnBreadcrumbs(): bool
	{
		return $this->visibility->breadcrumbs;
	}

	public function setBreadcrumbsVisibility(bool $visibility): void
	{
		$this->visibility->breadcrumbs = $visibility;
	}

	public function isVisibleOnSitemap(): bool
	{
		return $this->visibility->sitemap;
	}

	public function setSitemapVisibility(bool $visibility): void
	{
		$this->visibility->sitemap = $visibility;
	}

}
