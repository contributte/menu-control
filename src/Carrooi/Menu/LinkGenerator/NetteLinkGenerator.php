<?php

declare(strict_types=1);

namespace Carrooi\Menu\LinkGenerator;

use Carrooi\Menu\IMenuItem;
use Nette\Application\Application;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
final class NetteLinkGenerator implements ILinkGenerator
{


	/** @var \Nette\Application\Application */
	private $application;


	public function __construct(Application $application)
	{
		$this->application = $application;
	}


	public function link(IMenuItem $item): string
	{
		if (($action = $item->getAction()) !== null) {
			return $this->application->getPresenter()->link($action, $item->getActionParameters());

		} elseif (($link = $item->getLink()) !== null) {
			return $link;
		}

		return '#';
	}

}
