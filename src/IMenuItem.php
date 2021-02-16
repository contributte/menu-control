<?php

declare(strict_types=1);

namespace Contributte\MenuControl;

interface IMenuItem extends IMenuItemsContainer
{

	public function isActive(): bool;


	public function isAllowed(): bool;


	public function getAction(): ?string;


	/**
	 * @return array<string, string>
	 */
	public function getActionParameters(): array;


	/**
	 * @param array<string, string> $parameters
	 */
	public function setAction(string $target, array $parameters = []): void;


	public function getLink(): ?string;


	public function setLink(string $link): void;


	public function getRealTitle(): string;


	public function getRealLink(): string;


	public function getRealAbsoluteLink(): string;


	public function hasData(string $name): bool;


	/**
	 * @param ?string $default
	 * @return array<string, string>|string|null
	 */
	public function getData(?string $type = null, $default = null);


	/**
	 * @param array<string, string> $data
	 */
	public function setData(array $data): void;


	/**
	 * @param string $value
	 */
	public function addData(string $name, $value): void;


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
