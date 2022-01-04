<?php declare(strict_types = 1);

namespace Contributte\MenuControl\Localization;

use Nette\Localization\Translator;

final class ReturnTranslator implements Translator
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
