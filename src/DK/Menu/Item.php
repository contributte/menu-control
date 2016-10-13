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

	const ALLOWED_FOR_ACL = 'acl';

	const ALLOWED_BY_CALLBACK = 'callback';


	/** @var \DK\Menu\Menu */
	private $menu;

	/** @var string */
	private $title;

	/** @var string */
	private $target;

	/** @var bool For absolute (string) targets */
	private $absolute = false;

	/** @var array  */
	private $parameters = array();

	/** @var array  */
	private $data = array();

	/** @var bool  */
	private $visual = true;

	/** @var string */
	private $include;

	/** @var array  */
	private $allowedFor = array(
		self::ALLOWED_FOR_LOGGED_IN => null,
		self::ALLOWED_FOR_ACL => array(),
		self::ALLOWED_FOR_ROLES => array(),
		self::ALLOWED_FOR_MODULE => null,
		self::ALLOWED_FOR_PARAMETERS => array(),
		self::ALLOWED_BY_CALLBACK => null,
	);
	
	private $defaultAclPermission = 'view';

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
	 * @return string
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
	public function getTranslatedTitle()
	{
		$title = $this->getTitle();
		if (($translator = $this->getMenu()->getTranslator()) !== null) {
			$title = $translator->translate($title);
		}

		return $title;
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
	 * @param string $name
	 * @return bool
	 */
	public function hasData($name)
	{
		return isset($this->data[$name]);
	}


	/**
	 * @param string $name
	 * @return mixed
	 * @throws \DK\Menu\InvalidArgumentException
	 */
	public function getData($name = null)
	{
		if ($name !== null && !$this->hasData($name)) {
			throw new InvalidArgumentException("Undefined data '$name' in '$this->name' menu item.");
		}

		return $name === null ? $this->data : $this->data[$name];
	}


	/**
	 * @param array $data
	 * @return \DK\Menu\Item
	 */
	public function setData(array $data)
	{
		$this->data = $data;
		return $this;
	}


	/**
	 * @param string $name
	 * @param mixed $value
	 * @return \DK\Menu\Item
	 */
	public function addData($name, $value)
	{
		$this->data[$name] = $value;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function hasIcon()
	{
		return $this->hasData('icon');
	}


	/**
	 * @return string
	 */
	public function getIcon()
	{
		return $this->getData('icon');
	}


	/**
	 * @param string $icon
	 * @return \DK\Menu\Item
	 */
	public function setIcon($icon)
	{
		return $this->addData('icon', $icon);
	}


	/**
	 * @return bool
	 */
	public function hasCounter()
	{
		return $this->hasData('counter');
	}


	/**
	 * @return int
	 */
	public function getCounter()
	{
		return $this->getData('counter');
	}


	/**
	 * @param int $count
	 * @return \DK\Menu\Item
	 */
	public function setCounter($count)
	{
		return $this->addData('counter', $count);
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
	 * @return array|string
	 * @throws \DK\Menu\InvalidArgumentException
	 */
	public function getParsedInclude()
	{
		$include = $this->getInclude();
		$name = $this->getMenu()->getPresenter()->getName();

		if (($pos = mb_strpos($name, ':')) !== false) {
			$module = mb_substr($name, 0, $pos);
			$parse = function($include) use ($module) {
				return strtr($include, array(
					'<module>' => $module,
				));
			};

			if (is_string($include)) {
				return $parse($include);
			} elseif (is_array($include)) {
				return array_map($parse, $include);
			} else {
				throw new InvalidArgumentException;
			}
		}

		return $include;
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
	 * @param bool $absolute
	 * @return \DK\Menu\Item
	 */
	public function setAbsolute($absolute = true)
	{
		$this->absolute = $absolute;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAbsolute()
	{
		return $this->absolute === true;
	}

	/**
	 * @return bool
	 */
	public function hasAbsoluteTarget()
	{
		return preg_match('/^(http|https)\:\/\//', $this->getTarget()) === 1;
	}


	/**
	 * @return string
	 */
	public function getLink()
	{
		if ($this->hasAbsoluteTarget() || $this->isAbsolute()) {
			return $this->getTarget();
		} else {
			return $this->getMenu()->getPresenter()->link($this->getTarget(), $this->getParameters());
		}
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
	 * @return bool
	 */
	public function hasAllowedForAcl()
	{
		return count($this->allowedFor[self::ALLOWED_FOR_ACL]) > 0;
	}


	/**
	 * @return string
	 */
	public function getAllowedForAcl()
	{
		return $this->allowedFor[self::ALLOWED_FOR_ACL];
	}


	/**
	 * @param string $resource
	 * @param string $permission
	 * @return \DK\Menu\Item
	 */
	public function setAllowedForAcl($resource, $permission = null)
	{
		$this->allowedFor[self::ALLOWED_FOR_ACL]['resource'] = $resource;
		if ( $permission) {
			$this->allowedFor[self::ALLOWED_FOR_ACL]['permission'] = $permission;
		}

		return $this;
	}

	/**
	 * @param array $acl
	 * @return bool
	 */
	public function isAllowedForAcl($acl)
	{
		if (count($acl) === 0) {
			return true;
		}
		$resource = $acl['resource'];
		$permission = isset($acl['permission']) ? $acl['permission'] : $this->defaultAclPermission;
		return $this->getMenu()->getUser()->isAllowed($resource, $permission);
		
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

		$name = $this->getMenu()->getPresenter()->getName();
		if (($pos = mb_strpos($name, ':')) === false) {
			return false;
		}

		return mb_substr($name, 0, $pos) === ucfirst($module);
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
	public function hasAllowedByCallback()
	{
		return $this->allowedFor[self::ALLOWED_BY_CALLBACK] !== null;
	}


	/**
	 * @param callable $callback
	 * @return $this
	 */
	public function setAllowedByCallback(callable $callback)
	{
		$this->allowedFor[self::ALLOWED_BY_CALLBACK] = $callback;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function isAllowedByCallback()
	{
		if (!$this->hasAllowedByCallback()) {
			return true;
		}

		return (bool) call_user_func($this->allowedFor[self::ALLOWED_BY_CALLBACK], $this);
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		if ($this->allowed === null) {
			if (!$this->hasAllowedForAcl() && !$this->hasAllowedForLoggedIn() && !$this->hasAllowedForRoles() && !$this->hasAllowedForModule() && !$this->hasAllowedForParameters() && !$this->hasAllowedByCallback()) {
				return $this->allowed = true;
			}

			$this->allowed =
				$this->isAllowedForAcl($this->getAllowedForAcl()) &&
				$this->isAllowedForLoggedIn($this->getAllowedForLoggedIn()) &&
				$this->isAllowedByCallback() &&
				$this->isAllowedForRoles($this->getAllowedForRoles()) &&
				(
					$this->isAllowedForModule($this->getAllowedForModule()) ||
					$this->isAllowedForParameters($this->getAllowedForParameters())
				);
		}

		return $this->allowed;
	}


	/**
	 * @return bool
	 */
	public function isActive()
	{
		if ($this->active === null) {
			if (!$this->isAllowed() || $this->hasAbsoluteTarget()) {
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
					$include = $this->getParsedInclude();
					$name = $presenter->getName(). ':'. $presenter->getAction();

					if (is_string($include) && preg_match('~'. $this->getInclude(). '~', $name)) {
						$this->active = true;

					} elseif (is_array($include) && in_array($name, $include)) {
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
