<?php declare(strict_types = 1);

namespace Contributte\MenuControl;

use Contributte\MenuControl\Config\MenuItemAction;

interface IMenuItem extends IMenuItemsContainer
{

	public function isActive(): bool;

	public function isAllowed(): bool;

	public function getActionTarget(): ?string;

	/**
	 * @return array<string, string>
	 */
	public function getActionParameters(): array;

	public function setAction(MenuItemAction $action): void;

	public function getLink(): ?string;

	public function setLink(string $link): void;

	public function getRealTitle(): string;

	public function getRealLink(): string;

	public function getRealAbsoluteLink(): string;

	/**
	 * @return array<string, string>
	 */
	public function getData(): array;

	/**
	 * @param array<string, string> $data
	 */
	public function setData(array $data): void;

	public function hasDataItem(string $name): bool;

	public function getDataItem(string $name, ?string $default = null): ?string;

	public function addDataItem(string $name, string $value): void;

	/**
	 * @param string[] $include
	 */
	public function setInclude(array $include): void;

	public function isVisibleOnMenu(): bool;

	public function setMenuVisibility(bool $visibility): void;

	public function isVisibleOnBreadcrumbs(): bool;

	public function setBreadcrumbsVisibility(bool $visibility): void;

	public function isVisibleOnSitemap(): bool;

	public function setSitemapVisibility(bool $visibility): void;

}
