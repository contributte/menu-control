<?php

declare(strict_types=1);

namespace Carrooi\Menu\LinkGenerator;

use Carrooi\Menu\IMenuItem;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
final class NetteLinkGenerator implements ILinkGenerator
{

	/** @var \Nette\Application\LinkGenerator  */
	private $nativeLinkGenerator;



	public function __construct(\Nette\Application\LinkGenerator $nativeLinkGenerator)
	{
		$this->nativeLinkGenerator = $nativeLinkGenerator;
	}


	public function link(IMenuItem $item): string
	{
		if (($action = $item->getAction()) !== null) {
			return $this->nativeLinkGenerator->link($action);
			//return $this->application->getPresenter()->link($action, $item->getActionParameters());

		} elseif (($link = $item->getLink()) !== null) {
			return $link;
		}

		return '#';
	}

}
