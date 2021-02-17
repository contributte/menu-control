<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();
Tester\Environment::bypassFinals();
date_default_timezone_set('Europe/Prague');

if (!is_dir(__DIR__ . '/../tmp')) {
	mkdir(__DIR__ . '/../tmp/');
}

define('TEMP_DIR', __DIR__ . '/../tmp/'. getmypid());
Tester\Helpers::purge(TEMP_DIR);
