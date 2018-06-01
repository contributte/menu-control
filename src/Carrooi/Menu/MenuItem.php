<?php

declare(strict_types=1);

namespace Carrooi\Menu;

use Carrooi\Menu\LinkGenerator\ILinkGenerator;
use Carrooi\Menu\Security\IAuthorizator;
use Nette\Application\Application;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;
use Nette\Http\Request;
use Nette\Localization\ITranslator;

/**
 * @author David Kudera <kudera.d@gmail.com>
 */
final class MenuItem extends AbstractMenuItemsContainer implements IMenuItem
{


	/** @var string */
	private $title;

	/** @var array */
	private $action = [
		'target' => null,
		'parameters' => [],
	];

	/** @var string|null */
	private $link;

	/** @var array */
	private $data = [];

	/** @var bool[] */
	private $visibility = [
		'menu' => true,
		'breadcrumbs' => true,
		'sitemap' => true,
	];

	/** @var bool */
	private $active;

	/** @var string[] */
	private $include = [];

	
	public function __construct(ILinkGenerator $linkGenerator, ITranslator $translator, IAuthorizator $authorizator, LinkGenerator $nativeLinkGenerator, Request $httpRequest, IMenuItemFactory $menuItemFactory, string $title)
	{
		parent::__construct($linkGenerator, $translator, $authorizator,  $nativeLinkGenerator, $httpRequest, $menuItemFactory);

		$this->title = $title;
	}


	public function isActive(): bool
	{
		if ($this->active !== null) {
			return $this->active;
		}

		if (!$this->isAllowed()) {
			return $this->active = false;
		}

		if ($this->getAction()) {

			try {
				$this->nativeLinkGenerator->link($this->getAction(), $this->getActionParameters());
			} catch (InvalidLinkException $e) {}

			if ($this->presenter instanceof Presenter) {
				if ($this->presenter->getLastCreatedRequestFlag('current')) {
					return $this->active = true;
				}
			}

			if (!empty($this->include)) {
				$actionName = sprintf('%s:%s', $this->presenter->getName(), $this->presenter->getAction());
				foreach ($this->include as $include) {
					if (preg_match(sprintf('~%s~', $include), $actionName)) {
						return $this->active = true;
					}
				}
			}
		}

		foreach ($this->getItems() as $item) {
			if ($item->isAllowed() && $item->isActive()) {
				return $this->active = true;
			}
		}

		return $this->active = false;
	}


	public function isAllowed(): bool
	{
		return $this->authorizator->isMenuItemAllowed($this);
	}


	public function getAction(): ?string
	{
		return $this->action['target'];
	}


	public function getActionParameters(): array
	{
		return $this->action['parameters'];
	}


	public function setAction(string $target, array $parameters = []): void
	{
		$this->action['target'] = $target;
		$this->action['parameters'] = $parameters;
	}


	public function getLink(): ?string
	{
		return $this->link;
	}


	public function setLink(string $link): void
	{
		$this->link = $link;
	}


	public function getRealTitle(): string
	{
		return $this->translator->translate($this->title);
	}


	public function getRealLink(): string
	{
		return $this->linkGenerator->link($this);
	}


	public function getRealAbsoluteLink(): string
	{
		$url = $this->httpRequest->getUrl();
		$prefix = $url->getScheme(). '://'. $url->getHost();

		if ($url->getPort() !== 80) {
			$prefix .= ':'. $url->getPort();
		}

		return $prefix. $this->getRealLink();
	}


	public function hasData(string $name): bool
	{
		return array_key_exists($name, $this->data);
	}


	public function getData(string $name = null, $default = null)
	{
		if ($name === null) {
			return $this->data;
		}

		if (!$this->hasData($name)) {
			return $default;
		}

		return $this->data[$name];
	}


	public function setData(array $data): void
	{
		$this->data = $data;
	}


	public function addData(string $name, $value): void
	{
		$this->data[$name] = $value;
	}


	public function isVisibleOnMenu(): bool
	{
		return $this->visibility['menu'];
	}


	public function setMenuVisibility(bool $visibility): void
	{
		$this->visibility['menu'] = $visibility;
	}

	public function setInclude(array $include): void
	{
		$this->include = $include;
	}

	public function isVisibleOnBreadcrumbs(): bool
	{
		return $this->visibility['breadcrumbs'];
	}


	public function setBreadcrumbsVisibility(bool $visibility): void
	{
		$this->visibility['breadcrumbs'] = $visibility;
	}


	public function isVisibleOnSitemap(): bool
	{
		return $this->visibility['sitemap'];
	}


	public function setSitemapVisibility(bool $visibility): void
	{
		$this->visibility['sitemap'] = $visibility;
	}

}
