<?php declare(strict_types = 1);

namespace Contributte\MenuControl;

use Contributte\MenuControl\Config\MenuItemAction;
use Contributte\MenuControl\Config\MenuVisibility;
use Contributte\MenuControl\LinkGenerator\ILinkGenerator;
use Contributte\MenuControl\Security\IAuthorizator;
use Contributte\MenuControl\Traits\MenuItemData;
use Contributte\MenuControl\Traits\MenuItemVisibility;
use Nette\Localization\Translator;

final class MenuItem extends AbstractMenuItemsContainer implements IMenuItem
{

	use MenuItemData;
	use MenuItemVisibility;

	/** @var string */
	private $title;

	/** @var MenuItemAction|null */
	private $action;

	/** @var string|null */
	private $link;

	/** @var bool */
	private $active;

	/** @var string[] */
	private $include = [];

	public function __construct(
		IMenu $menu,
		ILinkGenerator $linkGenerator,
		Translator $translator,
		IAuthorizator $authorizator,
		IMenuItemFactory $menuItemFactory,
		string $title
	)
	{
		parent::__construct($menu, $linkGenerator, $translator, $authorizator, $menuItemFactory);

		$this->title = $title;
		$this->visibility = new MenuVisibility();
	}

	public function isActive(): bool
	{
		if ($this->active !== null) {
			return $this->active;
		}

		if (!$this->isAllowed()) {
			return $this->active = false;
		}

		if ($this->getActionTarget() && $this->menu->getActivePresenter()) {
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

	public function getActionTarget(): ?string
	{
		return $this->action !== null
			? $this->action->target
			: null;
	}

	/**
	 * @return array<string, string>
	 */
	public function getActionParameters(): array
	{
		return $this->action !== null ? $this->action->parameters : [];
	}

	public function setAction(MenuItemAction $action): void
	{
		$this->action = $action;
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
		return $this->linkGenerator->absoluteLink($this);
	}

	/**
	 * @param string[] $include
	 */
	public function setInclude(array $include): void
	{
		$this->include = $include;
	}

}
