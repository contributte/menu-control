<?php

declare(strict_types=1);

namespace Contributte\MenuControl\Localization;

use Nette\Localization\ITranslator;

final class ReturnTranslator implements ITranslator
{

	/**
	 * @param mixed $message
	 * @param mixed ...$parameters
	 */
	public function translate($message, ...$parameters): string
	{
		return $message;
	}

}
