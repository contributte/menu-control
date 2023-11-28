<?php declare(strict_types = 1);

namespace Contributte\MenuControl\Config;

final class MenuItemAction
{

	public string $target;

	/** @var array<string, mixed> */
	public array $parameters = [];

	/**
	 * @param array{target: string, parameters?: array<string, mixed>} $array
	 */
	public static function fromArray(array $array): self
	{
		$action = new self();
		$action->target = $array['target'];
		$action->parameters = $array['parameters'] ?? [];

		return $action;
	}

	public static function fromString(string $target): self
	{
		$action = new self();
		$action->target = $target;

		return $action;
	}

}
