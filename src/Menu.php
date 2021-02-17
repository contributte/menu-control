<?php

declare(strict_types=1);

namespace Contributte\MenuControl;

use Contributte\MenuControl\LinkGenerator\ILinkGenerator;
use Contributte\MenuControl\Loaders\IMenuLoader;
use Contributte\MenuControl\Security\IAuthorizator;
use Contributte\MenuControl\Config\TemplatePaths;
use Nette\Application\UI\Presenter;
use Nette\Localization\Translator;

final class Menu extends AbstractMenuItemsContainer implements IMenu
{

	/**
	 * @var IMenuLoader
	 */
	private $loader;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var \Contributte\MenuControl\Config\TemplatePaths
	 */
	private $templateConfig;

	/**
	 * @var Presenter
	 */
	private $activePresenter;

	public function __construct(
		ILinkGenerator $linkGenerator,
		Translator $translator,
		IAuthorizator $authorizator,
		IMenuItemFactory $menuItemFactory,
		IMenuLoader $loader,
		string $name,
		TemplatePaths $templateConfig
	) {
		parent::__construct($this, $linkGenerator, $translator, $authorizator, $menuItemFactory);

		$this->loader = $loader;
		$this->name = $name;
		$this->templateConfig = $templateConfig;
	}

	public function init(): void
	{
		$this->loader->load($this);
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getMenuTemplate(): string
	{
		return $this->templateConfig->menu;
	}

	public function getBreadcrumbsTemplate(): string
	{
		return $this->templateConfig->breadcrumbs;
	}

	public function getSitemapTemplate(): string
	{
		return $this->templateConfig->sitemap;
	}

	/**
	 * @return IMenuItem[]
	 */
	public function getPath(): array
	{
		$path = [];
		$parent = $this;

		while ($parent) {
			$item = $parent->findActiveItem();

			if (!$item) {
				break;
			}

			$parent = $path[] = $item;
		}

		return $path;
	}

	public function getActivePresenter(): ?Presenter
	{
		return $this->activePresenter;
	}

	public function setActivePresenter(?Presenter $presenter): void
	{
		$this->activePresenter = $presenter;
	}

}
