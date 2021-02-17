<?php

declare(strict_types=1);

namespace Contributte\MenuControl\Traits;

trait MenuItemVisibility
{

	/**
	 * @var bool[]
	 */
	private $visibility = [
		'menu' => true,
		'breadcrumbs' => true,
		'sitemap' => true,
	];

	public function isVisibleOnMenu(): bool
	{
		return $this->visibility['menu'];
	}

	public function setMenuVisibility(bool $visibility): void
	{
		$this->visibility['menu'] = $visibility;
	}

	public function isVisibleOnBreadcrumbs(): bool
	{
		return $this->visibility['breadcrumbs'];
	}

	public function setBreadcrumbsVisibility(bool $visibility): void
	{
		$this->visibility['breadcrumbs'] = $visibility;
	}

	public function isVisibleOnSitemap(): bool
	{
		return $this->visibility['sitemap'];
	}

	public function setSitemapVisibility(bool $visibility): void
	{
		$this->visibility['sitemap'] = $visibility;
	}

}
