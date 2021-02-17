<?php

declare(strict_types=1);

namespace Contributte\MenuControl;

use Contributte\MenuControl\LinkGenerator\ILinkGenerator;
use Contributte\MenuControl\Loaders\IMenuLoader;
use Contributte\MenuControl\Security\IAuthorizator;
use Contributte\MenuControl\UI\TemplateConfig;
use Nette\Application\UI\Presenter;
use Nette\Http\IRequest;
use Nette\Localization\ITranslator;

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
	 * @var TemplateConfig
	 */
	private $templateConfig;

	/**
	 * @var Presenter
	 */
	private $activePresenter;

	public function __construct(
		ILinkGenerator $linkGenerator,
		ITranslator $translator,
		IAuthorizator $authorizator,
		IRequest $httpRequest,
		IMenuItemFactory $menuItemFactory,
		IMenuLoader $loader,
		string $name,
		TemplateConfig $templateConfig
	) {
		parent::__construct($this, $linkGenerator, $translator, $authorizator, $httpRequest, $menuItemFactory);

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
