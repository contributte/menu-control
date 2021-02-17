<?php

declare(strict_types=1);

namespace Contributte\MenuControlTests;

use Contributte\MenuControl\AbstractMenuItemsContainer;
use Contributte\MenuControl\IMenu;
use Contributte\MenuControl\IMenuItem;
use Contributte\MenuControl\IMenuItemFactory;
use Contributte\MenuControl\LinkGenerator\ILinkGenerator;
use Contributte\MenuControl\Loaders\IMenuLoader;
use Contributte\MenuControl\Security\IAuthorizator;
use Contributte\MenuControl\Config\TemplatePaths;
use Nette\Application\Application;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\Presenter;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use Nette\Localization\Translator;
use Tester;

abstract class AbstractTestCase extends Tester\TestCase
{

	public function tearDown(): void
	{
		\Mockery::close();
	}

	protected function createMockMenu(?callable $fn = null): IMenu
	{
		return $this->createMock(IMenu::class, $fn);
	}

	protected function createMockMenuItem(?callable $fn = null): IMenuItem
	{
		return $this->createMock(IMenuItem::class, $fn);
	}

	protected function createMockMenuItemFactory(?callable $fn = null): IMenuItemFactory
	{
		return $this->createMock(IMenuItemFactory::class, $fn);
	}

	protected function createPartialMockAbstractMenuItemsContainer(
		?callable $fn = null,
		?array $args = null
	): AbstractMenuItemsContainer {
		return $this->createMock(AbstractMenuItemsContainer::class, $fn, $args, true);
	}

	protected function createMockMenuLoader(?callable $fn = null): IMenuLoader
	{
		return $this->createMock(IMenuLoader::class, $fn);
	}

	protected function createMockLinkGenerator(?callable $fn = null): ILinkGenerator
	{
		return $this->createMock(ILinkGenerator::class, $fn);
	}

	protected function createMockTemplateConfig(): TemplatePaths
	{
		$config = new TemplatePaths;
		$config->menu = 'menu-template.latte';
		$config->breadcrumbs = 'breadcrumbs-template.latte';
		$config->sitemap = 'sitemap-template.latte';

		return $config;
	}

	protected function createMockNetteLinkGenerator(?callable $fn = null): LinkGenerator
	{
		return $this->createMock(LinkGenerator::class, $fn);
	}

	protected function createMockTranslator(?callable $fn = null): Translator
	{
		return $this->createMock(Translator::class, $fn);
	}

	protected function createMockAuthorizator(?callable $fn = null): IAuthorizator
	{
		return $this->createMock(IAuthorizator::class, $fn);
	}

	protected function createMockApplication(?callable $fn = null): Application
	{
		return $this->createMock(Application::class, $fn);
	}

	protected function createMockPresenter(?callable $fn = null): Presenter
	{
		return $this->createMock(Presenter::class, $fn);
	}

	protected function createMockHttpRequest(?callable $fn = null): Request
	{
		return $this->createMock(Request::class, $fn);
	}

	protected function createMockHttpUrl(?callable $fn = null): UrlScript
	{
		return $this->createMock(UrlScript::class, $fn);
	}

	private function createMock(string $type, ?callable $fn = null, ?array $args = null, bool $partial = false)
	{
		$mock = $args === null ? \Mockery::mock($type) : \Mockery::mock($type, $args);

		if ($partial) {
			$mock->makePartial();
		}

		if ($fn) {
			$fn($mock);
		}

		return $mock;
	}

}
