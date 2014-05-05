<?php

namespace DK\Menu;

use Nette\Application\UI\InvalidLinkException;

/**
 *
 * @author David Kudera
 */
class Item extends Container
{


	const ALLOWED_FOR_LOGGED_IN = 'loggedIn';

	const ALLOWED_FOR_ROLES = 'roles';

	const ALLOWED_FOR_MODULE = 'module';

	const ALLOWED_FOR_PARAMETERS = 'parameters';


	/** @var \DK\Menu\Menu */
	private $menu;

	/** @var string */
	private $title;

	/** @var string */
	private $target;

	/** @var array  */
	private $parameters = array();

	/** @var bool  */
	private $visual = true;

	/** @var string */
	private $include;

	/** @var array  */
	private $allowedFor = array(
		self::ALLOWED_FOR_LOGGED_IN => null,
		self::ALLOWED_FOR_ROLES => array(),
		self::ALLOWED_FOR_MODULE => null,
		self::ALLOWED_FOR_PARAMETERS => array(),
	);


	/** @var bool */
	private $allowed;

	/** @var bool */
	private $active;


	/**
	 * @param \DK\Menu\Menu $menu
	 * @param string $title
	 * @param string $target
	 * @param array $parameters
	 */
	public function __construct(Menu $menu, $title, $target, array $parameters = array())
	{
		parent::__construct();

		$this->menu = $menu;

		$this->setTitle($title);
		$this->setTarget($target);
		$this->setParameters($parameters);
	}


	/**
	 * @return \DK\Menu\Menu
	 */
	public function getMenu()
	{
		return $this->menu;
	}


	/**
	 * @return \DK\Menu\Item
	 */
	public function getTitle()
	{
		return $this->title;
	}


	/**
	 * @param string $title
	 * @return \DK\Menu\Item
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getTarget()
	{
		return $this->target;
	}


	/**
	 * @param string $target
	 * @return \DK\Menu\Item
	 */
	public function setTarget($target)
	{
		$this->target = $target;
		return $this;
	}


	/**
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}


	/**
	 * @param array $parameters
	 * @return \DK\Menu\Item
	 */
	public function setParameters(array $parameters)
	{
		$this->parameters = $parameters;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function isVisual()
	{
		return $this->visual === true;
	}


	/**
	 * @param bool $visual
	 * @return \DK\Menu\Item
	 */
	public function setVisual($visual = true)
	{
		$this->visual = $visual;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function hasInclude()
	{
		return $this->include !== null;
	}


	/**
	 * @return string
	 */
	public function getInclude()
	{
		return $this->include;
	}


	/**
	 * @param string $include
	 * @return \DK\Menu\Item
	 */
	public function setInclude($include)
	{
		$this->include = $include;
		return $this;
	}


	/**
	 * @return \DK\Menu\Item
	 */
	public function invalidate()
	{
		$this->active = $this->allowed = null;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getLink()
	{
		return $this->getMenu()->getPresenter()->link($this->getTarget(), $this->getParameters());
	}


	/**
	 * @return bool
	 */
	public function hasAllowedForLoggedIn()
	{
		return $this->allowedFor[self::ALLOWED_FOR_LOGGED_IN] !== null;
	}


	/**
	 * @return bool
	 */
	public function getAllowedForLoggedIn()
	{
		return $this->allowedFor[self::ALLOWED_FOR_LOGGED_IN];
	}


	/**
	 * @param bool $loggedIn
	 * @return \DK\Menu\Item
	 */
	public function setAllowedForLoggedIn($loggedIn = true)
	{
		$this->allowedFor[self::ALLOWED_FOR_LOGGED_IN] = $loggedIn;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function hasAllowedForRoles()
	{
		return count($this->allowedFor[self::ALLOWED_FOR_ROLES]) > 0;
	}


	/**
	 * @return array
	 */
	public function getAllowedForRoles()
	{
		return $this->allowedFor[self::ALLOWED_FOR_ROLES];
	}


	/**
	 * @param array $roles
	 * @return \DK\Menu\Item
	 */
	public function setAllowedForRoles(array $roles)
	{
		$this->allowedFor[self::ALLOWED_FOR_ROLES] = $roles;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function hasAllowedForModule()
	{
		return $this->allowedFor[self::ALLOWED_FOR_MODULE] !== null;
	}


	/**
	 * @return string
	 */
	public function getAllowedForModule()
	{
		return $this->allowedFor[self::ALLOWED_FOR_MODULE];
	}


	/**
	 * @param string $module
	 * @return \DK\Menu\Item
	 */
	public function setAllowedForModule($module)
	{
		$this->allowedFor[self::ALLOWED_FOR_MODULE] = $module;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function hasAllowedForParameters()
	{
		return count($this->allowedFor[self::ALLOWED_FOR_PARAMETERS]) > 0;
	}


	/**
	 * @return array
	 */
	public function getAllowedForParameters()
	{
		return $this->allowedFor[self::ALLOWED_FOR_PARAMETERS];
	}


	/**
	 * @param array $parameters
	 * @return \DK\Menu\Item
	 */
	public function setAllowedForParameters(array $parameters)
	{
		$this->allowedFor[self::ALLOWED_FOR_PARAMETERS] = $parameters;
		return $this;
	}


	/**
	 * @param bool $loggedIn
	 * @return bool
	 */
	public function isAllowedForLoggedIn($loggedIn)
	{
		if ($loggedIn === null) {
			return true;
		}

		return $loggedIn === $this->getMenu()->getUser()->isLoggedIn();
	}


	/**
	 * @param array $roles
	 * @return bool
	 */
	public function isAllowedForRoles(array $roles = array())
	{
		if (count($roles) === 0) {
			return true;
		}

		foreach ($roles as $role) {
			if ($this->getMenu()->getUser()->isInRole($role)) {
				return true;
			}
		}

		return false;
	}


	/**
	 * @param string $module
	 * @return bool
	 */
	public function isAllowedForModule($module)
	{
		if ($module === null) {
			return true;
		}

		list($current) = explode(':', $this->getMenu()->getPresenter()->getName());
		return ucfirst($module) === $current;
	}


	/**
	 * @param array $parameters
	 * @return bool
	 */
	public function isAllowedForParameters(array $parameters)
	{
		if (count($parameters) === 0) {
			return true;
		}

		$params = $this->getMenu()->getPresenter()->getParameters();
		foreach ($parameters as $name => $value) {
			if (isset($params[$name]) && $params[$name] === $value) {
				return true;
			}
		}

		return false;
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		if ($this->allowed === null) {
			if (!$this->hasAllowedForLoggedIn() && !$this->hasAllowedForRoles() && !$this->hasAllowedForModule() && !$this->hasAllowedForParameters()) {
				return $this->allowed = true;
			}

			$this->allowed =
				$this->isAllowedForLoggedIn($this->getAllowedForLoggedIn()) &&
				$this->isAllowedForRoles($this->getAllowedForRoles()) &&
				(
					$this->isAllowedForModule($this->getAllowedForModule()) ||
					$this->isAllowedForParameters($this->getAllowedForParameters()
				));
		}

		return $this->allowed;
	}


	/**
	 * @return bool
	 */
	public function isActive()
	{
		if ($this->active === null) {
			if (!$this->isAllowed()) {
				$this->active = false;
			} else {
				$presenter = $this->getMenu()->getPresenter();

				try {
					$presenter->link($this->getTarget(), $this->getParameters());
				} catch (InvalidLinkException $e) {};

				if ($presenter->getLastCreatedRequestFlag('current')) {
					$this->active = true;
				}

				if ($this->active === null && $this->hasInclude()) {
					if (preg_match('~'. $this->getInclude(). '~', $presenter->getName(). ':'. $presenter->getAction())) {
						$this->active = true;
					}
				}

				if ($this->active === null && $this->hasItems()) {
					foreach ($this->getItems() as $item) {
						if ($item->isActive()) {
							$this->active = true;
							break;
						}
					}
				}

				if ($this->active === null) {
					$this->active = false;
				}
			}
		}

		return $this->active;
	}

} 