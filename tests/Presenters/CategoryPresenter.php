<?php

declare(strict_types=1);

namespace Contributte\MenuControlTests\Presenters;

use Contributte\MenuControl\UI\MenuComponent;
use Contributte\MenuControl\UI\MenuComponentFactory;
use Nette\Application\UI\Presenter;

final class CategoryPresenter extends Presenter
{

	/**
	 * @var MenuComponentFactory
	 */
	private $menuFactory;

	public function injectMenuComponentFactory(MenuComponentFactory $menuFactory): void
	{
		$this->menuFactory = $menuFactory;
	}

	protected function createComponentMenu(): MenuComponent
	{
		return $this->menuFactory->create('default');
	}

}
