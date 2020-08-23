<?php

declare(strict_types=1);

namespace Contributte\MenuControlTests\Menu;

use Contributte\MenuControl\IMenuItem;
use Contributte\MenuControl\MenuItemFactory;
use Contributte\MenuControlTests\TestCase;
use Tester\Assert;

require_once __DIR__. '/../../bootstrap.php';

/**
 * @testCase
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
final class MenuItemFactoryTest extends TestCase
{


	public function testCreate(): void
	{
		$menu = $this->createMockMenu();
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$factory = new MenuItemFactory;
		$item = $factory->create($menu, $linkGenerator, $translator, $authorizator, $request, $itemFactory, 'item');

		Assert::type(IMenuItem::class, $item);
	}

}

(new MenuItemFactoryTest)->run();
