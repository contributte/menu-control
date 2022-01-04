<?php declare(strict_types = 1);

namespace Contributte\MenuControl\Traits;

trait MenuItemData
{

	/** @var array<string, string> */
	private $data = [];

	/**
	 * @return array<string, string>
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * @param array<string, string> $data
	 */
	public function setData(array $data): void
	{
		$this->data = $data;
	}

	public function hasDataItem(string $name): bool
	{
		return array_key_exists($name, $this->data);
	}

	public function getDataItem(string $name, ?string $default = null): ?string
	{
		if (!array_key_exists($name, $this->data)) {
			return $default;
		}

		return $this->data[$name];
	}

	public function addDataItem(string $name, string $value): void
	{
		$this->data[$name] = $value;
	}

}
