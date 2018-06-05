<?php

declare(strict_types=1);

namespace CarrooiTests\Menu;

use Carrooi\Menu\MenuItem;
use CarrooiTests\TestCase;
use Mockery\MockInterface;
use Tester\Assert;

require_once __DIR__. '/../../bootstrap.php';

/**
 * @testCase
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
final class MenuItemTest extends TestCase
{


	public function testIsAllowed(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$nativeLinkGenerator = $this->createMockNativeLinkGenerator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$authorizator = $this->createMockAuthorizator(function(MockInterface $authorizator) {
			$authorizator->shouldReceive('isMenuItemAllowed')->andReturn(true);
		});

		$item = new MenuItem($linkGenerator, $translator, $authorizator, $nativeLinkGenerator, $request, $itemFactory, 'item');

		Assert::true($item->isAllowed());
	}


	public function testIsActive_not_allowed(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$nativeLinkGenerator = $this->createMockNativeLinkGenerator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$authorizator = $this->createMockAuthorizator(function(MockInterface $authorizator) {
			$authorizator->shouldReceive('isMenuItemAllowed')->andReturn(false);
		});

		$item = new MenuItem($linkGenerator, $translator, $authorizator, $nativeLinkGenerator, $request, $itemFactory, 'item');

		Assert::false($item->isActive());
	}


	public function testIsActive_action_current(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$authorizator = $this->createMockAuthorizator(function(MockInterface $authorizator) {
			$authorizator->shouldReceive('isMenuItemAllowed')->andReturn(true);
		});
		$presenter = $this->createMockPresenter(function(MockInterface $presenter) {
					$presenter->shouldReceive('link')->andReturn('#');
					$presenter->shouldReceive('getLastCreatedRequestFlag')->andReturn(true);
		});
		$nativeLinkGenerator = $this->createMockNativeLinkGenerator(function(MockInterface $nativeLinkGenerator) {
			$nativeLinkGenerator->shouldReceive('link')->andReturn('#');
		});

		$item = new MenuItem($linkGenerator, $translator, $authorizator, $nativeLinkGenerator, $request, $itemFactory, 'item');
		$item->setPresenter($presenter);
		$item->setAction('Home:');

		Assert::true($item->isActive());
	}

	public function testIsActive_include(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();
		$authorizator = $this->createMockAuthorizator(function(MockInterface $authorizator) {
			$authorizator->shouldReceive('isMenuItemAllowed')->andReturn(true);
		});
		$presenter = $this->createMockPresenter(function(MockInterface $presenter) {
			$presenter->shouldReceive('link')->andReturn('#');
			$presenter->shouldReceive('getLastCreatedRequestFlag')->andReturn(false);
			$presenter->shouldReceive('getName')->andReturn('Home');
			$presenter->shouldReceive('getAction')->andReturn('edit');
		});
		$nativeLinkGenerator = $this->createMockNativeLinkGenerator(function(MockInterface $nativeLinkGenerator) {
			$nativeLinkGenerator->shouldReceive('link')->andReturn('#');
		});
		$item = new MenuItem($linkGenerator, $translator, $authorizator, $nativeLinkGenerator, $request, $itemFactory, 'item');
		$item->setPresenter($presenter);
		$item->setAction('Home:');
		$item->setInclude([
			'^Home\:[a-zA-Z\:]+$'
		]);
		Assert::true($item->isActive());
	}

	public function testIsActive_active_child(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$nativeLinkGenerator = $this->createMockNativeLinkGenerator();
		$request = $this->createMockHttpRequest();

		$itemFactory = $this->createMockMenuItemFactory(function(MockInterface $itemFactory) {
			$itemFactory->shouldReceive('create')->andReturn(
				$this->createMockMenuItem(function(MockInterface $child) {
					$child->shouldReceive('isAllowed')->andReturn(true);
					$child->shouldReceive('isActive')->andReturn(true);
				})
			);
		});

		$authorizator = $this->createMockAuthorizator(function(MockInterface $authorizator) {
			$authorizator->shouldReceive('isMenuItemAllowed')->andReturn(true);
		});


		$item = new MenuItem($linkGenerator, $translator, $authorizator, $nativeLinkGenerator, $request, $itemFactory, 'item');
		$item->addItem('child', 'Child');

		Assert::true($item->isActive());
	}


	public function testAction(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$nativeLinkGenerator = $this->createMockNativeLinkGenerator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$item = new MenuItem($linkGenerator, $translator, $authorizator, $nativeLinkGenerator, $request, $itemFactory, 'item');

		Assert::null($item->getAction());
		Assert::equal([], $item->getActionParameters());

		$item->setAction('Home:', ['back' => 'login']);

		Assert::same('Home:', $item->getAction());
		Assert::equal(['back' => 'login'], $item->getActionParameters());
	}


	public function testLink(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$nativeLinkGenerator = $this->createMockNativeLinkGenerator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$item = new MenuItem($linkGenerator, $translator, $authorizator, $nativeLinkGenerator, $request, $itemFactory, 'item');

		Assert::null($item->getLink());

		$item->setLink('#');

		Assert::same('#', $item->getLink());
	}


	public function testGetRealTitle(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$authorizator = $this->createMockAuthorizator();
		$nativeLinkGenerator = $this->createMockNativeLinkGenerator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$translator = $this->createMockTranslator(function(MockInterface $translator) {
			$translator->shouldReceive('translate')->andReturn('ITEM');
		});

		$item = new MenuItem($linkGenerator, $translator, $authorizator, $nativeLinkGenerator, $request, $itemFactory, 'item');

		Assert::same('ITEM', $item->getRealTitle());
	}


	public function testGetRealLink(): void
	{
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$nativeLinkGenerator = $this->createMockNativeLinkGenerator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$linkGenerator = $this->createMockLinkGenerator(function(MockInterface $linkGenerator) {
			$linkGenerator->shouldReceive('link')->andReturn('#');
		});

		$item = new MenuItem($linkGenerator, $translator, $authorizator, $nativeLinkGenerator, $request, $itemFactory, 'item');

		Assert::same('#', $item->getRealLink());
	}


	public function testGetRealAbsoluteLink_80_port(): void
	{
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$nativeLinkGenerator = $this->createMockNativeLinkGenerator();
		$itemFactory = $this->createMockMenuItemFactory();

		$linkGenerator = $this->createMockLinkGenerator(function(MockInterface $linkGenerator) {
			$linkGenerator->shouldReceive('link')->andReturn('/');
		});

		$request = $this->createMockHttpRequest(function(MockInterface $request) {
			$request->shouldReceive('getUrl')->andReturn(
				$this->createMockHttpUrl(function(MockInterface $url) {
					$url->shouldReceive('getScheme')->andReturn('https');
					$url->shouldReceive('getHost')->andReturn('localhost');
					$url->shouldReceive('getPort')->andReturn(80);
				})
			);
		});

		$item = new MenuItem($linkGenerator, $translator, $authorizator, $nativeLinkGenerator, $request, $itemFactory, 'item');

		Assert::same('https://localhost/', $item->getRealAbsoluteLink());
	}


	public function testGetRealAbsoluteLink(): void
	{
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$nativeLinkGenerator = $this->createMockNativeLinkGenerator();
		$itemFactory = $this->createMockMenuItemFactory();

		$linkGenerator = $this->createMockLinkGenerator(function(MockInterface $linkGenerator) {
			$linkGenerator->shouldReceive('link')->andReturn('/');
		});

		$request = $this->createMockHttpRequest(function(MockInterface $request) {
			$request->shouldReceive('getUrl')->andReturn(
				$this->createMockHttpUrl(function(MockInterface $url) {
					$url->shouldReceive('getScheme')->andReturn('https');
					$url->shouldReceive('getHost')->andReturn('localhost');
					$url->shouldReceive('getPort')->andReturn(8080);
				})
			);
		});

		$item = new MenuItem($linkGenerator, $translator, $authorizator, $nativeLinkGenerator, $request, $itemFactory, 'item');

		Assert::same('https://localhost:8080/', $item->getRealAbsoluteLink());
	}


	public function testGetData(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$nativeLinkGenerator = $this->createMockNativeLinkGenerator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$item = new MenuItem($linkGenerator, $translator, $authorizator, $nativeLinkGenerator, $request, $itemFactory, 'item');
		$item->setData(['test' => 'data']);

		Assert::equal('data', $item->getData('test'));
	}


	public function testGetData_default(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$nativeLinkGenerator = $this->createMockNativeLinkGenerator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$item = new MenuItem($linkGenerator, $translator, $authorizator, $nativeLinkGenerator, $request, $itemFactory, 'item');

		Assert::equal('default', $item->getData('test', 'default'));
	}


	public function testAddData(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$nativeLinkGenerator = $this->createMockNativeLinkGenerator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$item = new MenuItem($linkGenerator, $translator, $authorizator, $nativeLinkGenerator, $request, $itemFactory, 'item');
		$item->addData('test', 'data');

		Assert::true($item->hasData('test'));
		Assert::equal('data', $item->getData('test'));
	}


	public function testVisibility(): void
	{
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$nativeLinkGenerator = $this->createMockNativeLinkGenerator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$item = new MenuItem($linkGenerator, $translator, $authorizator, $nativeLinkGenerator, $request, $itemFactory, 'item');

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
