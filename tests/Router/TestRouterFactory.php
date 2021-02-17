<?php

declare(strict_types=1);

namespace Contributte\MenuControlTests\Router;

use Nette\Application\Routers\RouteList;

final class TestRouterFactory
{

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$router->addRoute('<presenter>[/<action>[/<id>]]', 'Homepage:default');

		return $router;
	}

}
