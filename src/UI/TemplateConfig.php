<?php

declare(strict_types=1);

namespace Contributte\MenuControl\UI;

final class TemplateConfig
{

	/**
	 * @var string
	 */
	public $menu = __DIR__ . '/templates/menu.latte';

	/**
	 * @var string
	 */
	public $breadcrumbs = __DIR__ . '/templates/breadcrumbs.latte';

	/**
	 * @var string
	 */
	public $sitemap = __DIR__ . '/templates/sitemap.latte';

}
