<?php declare(strict_types = 1);

namespace Contributte\MenuControl\LinkGenerator;

use Contributte\MenuControl\IMenuItem;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\InvalidLinkException;
use Nette\Http\IRequest;

final class NetteLinkGenerator implements ILinkGenerator
{

	private IRequest $httpRequest;

	private LinkGenerator $linkGenerator;

	public function __construct(IRequest $httpRequest, LinkGenerator $linkGenerator)
	{
		$this->httpRequest = $httpRequest;
		$this->linkGenerator = $linkGenerator;
	}

	public function link(IMenuItem $item): string
	{
		$action = $item->getActionTarget();
		if ($action !== null) {
			try {
				return $this->linkGenerator->link($action, $item->getActionParameters());
			} catch (InvalidLinkException) {
			}
		}

		$link = $item->getLink();
		if ($link !== null) {
			return $link;
		}

		return '#';
	}

	public function absoluteLink(IMenuItem $item): string
	{
		$url = $this->httpRequest->getUrl();
		$prefix = $url->getScheme() . '://' . $url->getHost();

		if ($url->getPort() !== 80) {
			$prefix .= ':' . $url->getPort();
		}

		return $prefix . $this->link($item);
	}

}
