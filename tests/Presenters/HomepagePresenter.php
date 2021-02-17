<?php

declare(strict_types=1);

namespace Contributte\MenuControlTests\Presenters;

use Contributte\MenuControl\UI\IMenuComponentFactory;
use Contributte\MenuControl\UI\MenuComponent;
use Nette\Application\UI\Presenter;

final class HomepagePresenter extends Presenter
{

	/**
	 * @var IMenuComponentFactory
	 */
	private $menuFactory;

	public function injectMenuComponentFactory(IMenuComponentFactory $menuFactory): void
	{
		$this->menuFactory = $menuFactory;
	}

	protected function createComponentMenu(): MenuComponent
	{
		return $this->menuFactory->create('default');
	}

}
