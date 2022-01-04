<?php declare(strict_types = 1);

namespace Tests\Cases;

use Contributte\MenuControl\Config\MenuItemAction;
use Contributte\MenuControl\MenuItem;
use Mockery\MockInterface;
use Tester\Assert;
use Tests\Toolkit\AbstractTestCase;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
final class MenuItemTest extends AbstractTestCase
{

	public function testIsAllowed(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$itemFactory = $this->createMockMenuItemFactory();

		$menu = $this->createMockMenu(function (MockInterface $menu): void {
			$menu->shouldReceive('getActivePresenter')->andReturn(
				$this->createMockPresenter(function (MockInterface $presenter): void {
					$presenter->shouldReceive('link')->andReturn('#');
					$presenter->shouldReceive('getLastCreatedRequestFlag')->andReturn(true);
				})
			);
		});

		$authorizator = $this->createMockAuthorizator(function (MockInterface $authorizator): void {
			$authorizator->shouldReceive('isMenuItemAllowed')->andReturn(true);
		});

		$item = new MenuItem($menu, $linkGenerator, $translator, $authorizator, $itemFactory, 'item');

		Assert::true($item->isAllowed());
	}

	public function testIsActive_not_allowed(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$itemFactory = $this->createMockMenuItemFactory();

		$menu = $this->createMockMenu(function (MockInterface $menu): void {
			$menu->shouldReceive('getActivePresenter')->andReturn(
				$this->createMockPresenter(function (MockInterface $presenter): void {
					$presenter->shouldReceive('link')->andReturn('#');
					$presenter->shouldReceive('getLastCreatedRequestFlag')->andReturn(true);
				})
			);
		});

		$authorizator = $this->createMockAuthorizator(function (MockInterface $authorizator): void {
			$authorizator->shouldReceive('isMenuItemAllowed')->andReturn(false);
		});

		$item = new MenuItem($menu, $linkGenerator, $translator, $authorizator, $itemFactory, 'item');

		Assert::false($item->isActive());
	}

	public function testIsActive_action_current(): void
	{
		$translator = $this->createMockTranslator();
		$itemFactory = $this->createMockMenuItemFactory();

		$linkGenerator = $this->createMockLinkGenerator(function (MockInterface $linkGenerator): void {
			$linkGenerator->shouldReceive('link')->andReturn('#');
		});

		$menu = $this->createMockMenu(function (MockInterface $menu): void {
			$menu->shouldReceive('getActivePresenter')->andReturn(
				$this->createMockPresenter(function (MockInterface $presenter): void {
					$presenter->shouldReceive('link')->andReturn('#');
				})
			);
		});

		$authorizator = $this->createMockAuthorizator(function (MockInterface $authorizator): void {
			$authorizator->shouldReceive('isMenuItemAllowed')->andReturn(true);
		});

		$item = new MenuItem($menu, $linkGenerator, $translator, $authorizator, $itemFactory, 'item');
		$item->setAction(MenuItemAction::fromString('Home:'));

		Assert::true($item->isActive());
	}

	public function testIsActive_include(): void
	{
		$translator = $this->createMockTranslator();
		$itemFactory = $this->createMockMenuItemFactory();

		$linkGenerator = $this->createMockLinkGenerator(function (MockInterface $linkGenerator): void {
			$linkGenerator->shouldReceive('link')->andReturn('/home');
		});

		$menu = $this->createMockMenu(function (MockInterface $menu): void {
			$menu->shouldReceive('getActivePresenter')->andReturn(
				$this->createMockPresenter(function (MockInterface $presenter): void {
					$presenter->shouldReceive('link')->andReturn('/home/edit');
					$presenter->shouldReceive('getName')->andReturn('Home');
					$presenter->shouldReceive('getAction')->andReturn('edit');
				})
			);
		});

		$authorizator = $this->createMockAuthorizator(function (MockInterface $authorizator): void {
			$authorizator->shouldReceive('isMenuItemAllowed')->andReturn(true);
		});

		$item = new MenuItem($menu, $linkGenerator, $translator, $authorizator, $itemFactory, 'item');
		$item->setAction(MenuItemAction::fromString('Home:'));
		$item->setInclude([
			'^Home\:[a-zA-Z\:]+$',
		]);

		Assert::true($item->isActive());
	}

	public function testIsActive_active_child(): void
	{
		$menu = $this->createMockMenu();
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();

		$itemFactory = $this->createMockMenuItemFactory(function (MockInterface $itemFactory): void {
			$itemFactory->shouldReceive('create')->andReturn(
				$this->createMockMenuItem(function (MockInterface $child): void {
					$child->shouldReceive('isAllowed')->andReturn(true);
					$child->shouldReceive('isActive')->andReturn(true);
				})
			);
		});

		$authorizator = $this->createMockAuthorizator(function (MockInterface $authorizator): void {
			$authorizator->shouldReceive('isMenuItemAllowed')->andReturn(true);
		});

		$item = new MenuItem($menu, $linkGenerator, $translator, $authorizator, $itemFactory, 'item');
		$item->addItem('child', 'Child');

		Assert::true($item->isActive());
	}

	public function testAction(): void
	{
		$menu = $this->createMockMenu();
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$itemFactory = $this->createMockMenuItemFactory();

		$item = new MenuItem($menu, $linkGenerator, $translator, $authorizator, $itemFactory, 'item');

		Assert::null($item->getActionTarget());
		Assert::equal([], $item->getActionParameters());

		$item->setAction(MenuItemAction::fromArray([
			'target' => 'Home:',
			'parameters' => ['back' => 'login'],
		]));

		Assert::same('Home:', $item->getActionTarget());
		Assert::equal(['back' => 'login'], $item->getActionParameters());
	}

	public function testLink(): void
	{
		$menu = $this->createMockMenu();
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$itemFactory = $this->createMockMenuItemFactory();

		$item = new MenuItem($menu, $linkGenerator, $translator, $authorizator, $itemFactory, 'item');

		Assert::null($item->getLink());

		$item->setLink('#');

		Assert::same('#', $item->getLink());
	}

	public function testGetRealTitle(): void
	{
		$menu = $this->createMockMenu();
		$linkGenerator = $this->createMockLinkGenerator();
		$authorizator = $this->createMockAuthorizator();
		$itemFactory = $this->createMockMenuItemFactory();

		$translator = $this->createMockTranslator(function (MockInterface $translator): void {
			$translator->shouldReceive('translate')->andReturn('ITEM');
		});

		$item = new MenuItem($menu, $linkGenerator, $translator, $authorizator, $itemFactory, 'item');

		Assert::same('ITEM', $item->getRealTitle());
	}

	public function testGetRealLink(): void
	{
		$menu = $this->createMockMenu();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$itemFactory = $this->createMockMenuItemFactory();

		$linkGenerator = $this->createMockLinkGenerator(function (MockInterface $linkGenerator): void {
			$linkGenerator->shouldReceive('link')->andReturn('#');
		});

		$item = new MenuItem($menu, $linkGenerator, $translator, $authorizator, $itemFactory, 'item');

		Assert::same('#', $item->getRealLink());
	}

	public function testGetRealAbsoluteLink_80_port(): void
	{
		$menu = $this->createMockMenu();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$itemFactory = $this->createMockMenuItemFactory();

		$linkGenerator = $this->createMockLinkGenerator(function (MockInterface $linkGenerator): void {
			$linkGenerator->shouldReceive('absoluteLink')->andReturn('https://localhost/');
		});

		$item = new MenuItem($menu, $linkGenerator, $translator, $authorizator, $itemFactory, 'item');

		Assert::same('https://localhost/', $item->getRealAbsoluteLink());
	}

	public function testGetRealAbsoluteLink(): void
	{
		$menu = $this->createMockMenu();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$itemFactory = $this->createMockMenuItemFactory();

		$linkGenerator = $this->createMockLinkGenerator(function (MockInterface $linkGenerator): void {
			$linkGenerator->shouldReceive('absoluteLink')->andReturn('https://localhost:8080/');
		});

		$item = new MenuItem($menu, $linkGenerator, $translator, $authorizator, $itemFactory, 'item');

		Assert::same('https://localhost:8080/', $item->getRealAbsoluteLink());
	}

	public function testGetData(): void
	{
		$menu = $this->createMockMenu();
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$itemFactory = $this->createMockMenuItemFactory();

		$item = new MenuItem($menu, $linkGenerator, $translator, $authorizator, $itemFactory, 'item');
		$item->setData(['test' => 'data']);

		Assert::equal(['test' => 'data'], $item->getData());
		Assert::equal('data', $item->getDataItem('test'));
	}

	public function testGetDataItemDefault(): void
	{
		$menu = $this->createMockMenu();
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$itemFactory = $this->createMockMenuItemFactory();

		$item = new MenuItem($menu, $linkGenerator, $translator, $authorizator, $itemFactory, 'item');

		Assert::equal('default', $item->getDataItem('test', 'default'));
	}

	public function testAddDataItem(): void
	{
		$menu = $this->createMockMenu();
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$itemFactory = $this->createMockMenuItemFactory();

		$item = new MenuItem($menu, $linkGenerator, $translator, $authorizator, $itemFactory, 'item');
		$item->addDataItem('test', 'data');

		Assert::true($item->hasDataItem('test'));
		Assert::equal('data', $item->getDataItem('test'));
	}

	public function testVisibility(): void
	{
		$menu = $this->createMockMenu();
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$itemFactory = $this->createMockMenuItemFactory();

		$item = new MenuItem($menu, $linkGenerator, $translator, $authorizator, $itemFactory, 'item');

		Assert::true($item->isVisibleOnMenu());
		$item->setMenuVisibility(false);
		Assert::false($item->isVisibleOnMenu());

		Assert::true($item->isVisibleOnBreadcrumbs());
		$item->setBreadcrumbsVisibility(false);
		Assert::false($item->isVisibleOnBreadcrumbs());

		Assert::true($item->isVisibleOnSitemap());
		$item->setSitemapVisibility(false);
		Assert::false($item->isVisibleOnSitemap());
	}

}

(new MenuItemTest())->run();
