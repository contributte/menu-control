<?php

declare(strict_types=1);

namespace Carrooi\Menu;

use Nette\Application\UI\Presenter;

interface IMenu extends IMenuItemsContainer
{

	public function getName(): string;


	public function getMenuTemplate(): string;


	public function getBreadcrumbsTemplate(): string;


	public function getSitemapTemplate(): string;


	public function getPath(): array;


	public function getActivePresenter(): ?Presenter;


	public function setActivePresenter(?Presenter $link): void;

}
