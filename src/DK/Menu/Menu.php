<?php

namespace DK\Menu;

use Nette\Application\Application;
use Nette\Application\UI\Presenter;
use Nette\Security\User;
use Nette\Localization\ITranslator;

/**
 *
 * @author David Kudera
 */
class Menu extends Container
{


	/** @var \Nette\Application\UI\Presenter */
	private $presenter;

	/** @var \Nette\Security\User */
	private $user;

	/** @var \Nette\Localization\ITranslator */
	private $translator;


	/**
	 * @param \Nette\Security\User $user
	 * @param string $name
	 */
	public function __construct(User $user, $name)
	{
		parent::__construct(null, $name);

		$this->user = $user;
	}


	/**
	 * @param \Nette\Application\Application $application
	 * @return \DK\Menu\Menu
	 */
	public function init(Application $application)
	{
		if (($presenter = $application->getPresenter()) !== null) {		/** @var $presenter \Nette\Application\UI\Presenter */
			$this->setPresenter($presenter);
		} else {
			$_this = $this;

			$application->onPresenter[] = function(Application $application, Presenter $presenter) use ($_this) {
				$_this->setPresenter($presenter);
			};
		}

		return $this;
	}


	/**
	 * @return \Nette\Localization\ITranslator
	 */
	public function getTranslator()
	{
		return $this->translator;
	}


	/**
	 * @param \Nette\Localization\ITranslator $translator
	 * @return \DK\Menu\Menu
	 */
	public function setTranslator(ITranslator $translator)
	{
		$this->translator = $translator;
		return $this;
	}


	/**
	 * @return \DK\Menu\Menu
	 */
	public function getMenu()
	{
		return $this;
	}


	/**
	 * @return \Nette\Application\UI\Presenter
	 */
	public function getPresenter()
	{
		return $this->presenter;
	}


	/**
	 * @param \Nette\Application\UI\Presenter $presenter
	 * @return \DK\Menu\Menu
	 */
	public function setPresenter(Presenter $presenter)
	{
		$this->presenter = $presenter;
		return $this;
	}


	/**
	 * @return \Nette\Security\User
	 */
	public function getUser()
	{
		return $this->user;
	}


	/**
	 * @return \DK\Menu\Item[]
	 */
	public function getPath()
	{
		$path = array();
		$parent = $this;

		do {
			$item = $parent->getCurrentItem();
			if ($item === null) {
				$parent = null;
			} else {
				$parent = $path[] = $item;
			}
		} while ($parent !== null);

		return $path;
	}


	/**
	 * @return \DK\Menu\Item
	 */
	public function getLastCurrentItem()
	{
		$path = $this->getPath();
		$item = end($path);
		return $item === false ? null : $item;
	}

} 