<?php

declare(strict_types=1);

namespace Carrooi\Menu;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
interface IMenu extends IMenuItemsContainer
{


	public function getName(): string;


	public function getMenuTemplate(): string;


	public function getBreadcrumbsTemplate(): string;


	public function getSitemapTemplate(): string;


	public function getPath(): array;

}
