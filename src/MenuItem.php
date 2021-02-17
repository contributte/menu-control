<?php

declare(strict_types=1);

namespace Contributte\MenuControl;

use Contributte\MenuControl\LinkGenerator\ILinkGenerator;
use Contributte\MenuControl\Security\IAuthorizator;
use Contributte\MenuControl\Traits\MenuItemData;
use Contributte\MenuControl\Traits\MenuItemVisibility;
use Nette\Http\IRequest;
use Nette\Localization\Translator;

final class MenuItem extends AbstractMenuItemsContainer implements IMenuItem
{

	use MenuItemData;
	use MenuItemVisibility;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var array{'target': ?string, "parameters": array}
	 */
	private $action = [
		'target' => null,
		'parameters' => [],
	];

	/**
	 * @var string|null
	 */
	private $link;

	/**
	 * @var bool
	 */
	private $active;

	/**
	 * @var string[]
	 */
	private $include = [];

	public function __construct(
		IMenu $menu,
		ILinkGenerator $linkGenerator,
		Translator $translator,
		IAuthorizator $authorizator,
		IRequest $httpRequest,
		IMenuItemFactory $menuItemFactory,
		string $title
	) {
		parent::__construct($menu, $linkGenerator, $translator, $authorizator, $httpRequest, $menuItemFactory);

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

		if ($this->getAction() && $this->menu->getActivePresenter()) {
			$presenter = $this->menu->getActivePresenter();
			if ($presenter->link('//this') === $this->linkGenerator->link($this)) {
				return $this->active = true;
			}

			if ($this->include) {
				$actionName = sprintf('%s:%s', $presenter->getName(), $presenter->getAction());
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

	/**
	 * @return array<string, string>
	 */
	public function getActionParameters(): array
	{
		return $this->action['parameters'];
	}

	/**
	 * @param array<string, string> $parameters
	 */
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

	/**
	 * @param string[] $include
	 */
	public function setInclude(array $include): void
	{
		$this->include = $include;
	}

}
