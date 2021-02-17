<?php

declare(strict_types=1);

namespace Contributte\MenuControl\Traits;

trait MenuItemData
{

	/**
	 * @var array<string, string>
	 */
	private $data = [];

	public function hasData(string $name): bool
	{
		return array_key_exists($name, $this->data);
	}

	/**
	 * @param ?string $default
	 * @return array<string, string>|string|null
	 */
	public function getData(?string $name = null, $default = null)
	{
		if ($name === null) {
			return $this->data;
		}

		if (!$this->hasData($name)) {
			return $default;
		}

		return $this->data[$name];
	}

	/**
	 * @param array<string, string> $data
	 */
	public function setData(array $data): void
	{
		$this->data = $data;
	}

	/**
	 * @param string $value
	 */
	public function addData(string $name, $value): void
	{
		$this->data[$name] = $value;
	}

}
