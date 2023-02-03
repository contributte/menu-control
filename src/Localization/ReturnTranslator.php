<?php declare(strict_types = 1);

namespace Contributte\MenuControl\Localization;

use Nette\Localization\Translator;
use Stringable;

final class ReturnTranslator implements Translator
{

	public function translate(string|Stringable $message, mixed ...$parameters): string|Stringable
	{
		return $message;
	}

}
