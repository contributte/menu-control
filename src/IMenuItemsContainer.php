<?php

declare(strict_types=1);

namespace Contributte\MenuControl;

use Contributte\MenuControl\LinkGenerator\ILinkGenerator;

interface IMenuItemsContainer
{

	public function setLinkGenerator(ILinkGenerator $linkGenerator): void;


	/**
	 * @return IMenuItem[]
	 */
	public function getItems(): array;


	public function getItem(string $name): IMenuItem;


	public function addItem(string $name, string $title, ?callable $fn = null): void;


	public function findActiveItem(): ?IMenuItem;


	public function hasVisibleItemsOnMenu(): bool;


	public function hasVisibleItemsOnBreadcrumbs(): bool;


	public function hasVisibleItemsOnSitemap(): bool;


	/**
	 * @return IMenuItem[]
	 */
	public function getVisibleItemsOnMenu(): array;


	/**
	 * @return IMenuItem[]
	 */
	public function getVisibleItemsOnBreadcrumbs(): array;


	/**
	 * @return IMenuItem[]
	 */
	public function getVisibleItemsOnSitemap(): array;

}
