<?php

declare(strict_types=1);

namespace CarrooiTests\Menu;

use Carrooi\Menu\IMenuItem;
use Carrooi\Menu\MenuItemFactory;
use CarrooiTests\TestCase;
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
		$linkGenerator = $this->createMockLinkGenerator();
		$translator = $this->createMockTranslator();
		$authorizator = $this->createMockAuthorizator();
		$application = $this->createMockApplication();
		$request = $this->createMockHttpRequest();
		$itemFactory = $this->createMockMenuItemFactory();

		$factory = new MenuItemFactory;
		$item = $factory->create($linkGenerator, $translator, $authorizator, $application, $request, $itemFactory, 'item');

		Assert::type(IMenuItem::class, $item);
	}

}

(new MenuItemFactoryTest)->run();
