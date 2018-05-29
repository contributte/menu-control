<?php

declare(strict_types=1);

namespace Carrooi\Menu;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
interface IMenuItem extends IMenuItemsContainer
{


	public function isActive(): bool;


	public function isAllowed(): bool;


	public function getAction(): ?string;


	public function getActionParameters(): array;


	public function setAction(string $target, array $parameters = []): void;


	public function getLink(): ?string;


	public function setLink(string $link): void;


	public function getRealTitle(): string;


	public function getRealLink(): string;


	public function getRealAbsoluteLink(): string;


	public function hasData(string $name): bool;


	public function getData(string $type = null, $default = null);


	public function setData(array $data): void;


	public function addData(string $name, $value): void;


	public function setInclude(array $include) : void;


	public function isVisibleOnMenu(): bool;


	public function setMenuVisibility(bool $visibility): void;


	public function isVisibleOnBreadcrumbs(): bool;


	public function setBreadcrumbsVisibility(bool $visibility): void;


	public function isVisibleOnSitemap(): bool;


	public function setSitemapVisibility(bool $visibility): void;

}
