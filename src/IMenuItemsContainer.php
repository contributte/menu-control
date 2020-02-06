<?php

declare(strict_types=1);

namespace Carrooi\Menu;

use Carrooi\Menu\LinkGenerator\ILinkGenerator;

interface IMenuItemsContainer
{

	public function setLinkGenerator(ILinkGenerator $linkGenerator): void;


	public function getItems(): array;


	public function getItem(string $name): IMenuItem;


	public function addItem(string $name, string $title, ?callable $fn = null): void;


	public function findActiveItem(): ?IMenuItem;


	public function hasVisibleItemsOnMenu(): bool;


	public function hasVisibleItemsOnBreadcrumbs(): bool;


	public function hasVisibleItemsOnSitemap(): bool;


	public function getVisibleItemsOnMenu(): array;


	public function getVisibleItemsOnBreadcrumbs(): array;


	public function getVisibleItemsOnSitemap(): array;

}
