<?php

namespace DKTests\Menu;

use Nette\Security\IUserStorage;
use Nette\Security\IIdentity;

/**
 *
 * @author David Kudera
 */
class MockUserStorage implements IUserStorage
{


	/** @var bool  */
	private $auth = false;

	/** @var \Nette\Security\IIdentity */
	private $identity;


	/**
	 * @param bool $state
	 */
	function setAuthenticated($state)
	{
		$this->auth = $state;
	}


	/**
	 * @return bool
	 */
	function isAuthenticated()
	{
		return $this->auth;
	}


	/**
	 * @param \Nette\Security\IIdentity $identity
	 */
	function setIdentity(IIdentity $identity = NULL)
	{
		$this->identity = $identity;
	}


	/**
	 * @return \Nette\Security\IIdentity
	 */
	function getIdentity()
	{
		return $this->identity;
	}


	/**
	 * @param int $time
	 * @param int $flags
	 */
	function setExpiration($time, $flags = 0)
	{

	}


	function getLogoutReason()
	{

	}

}
