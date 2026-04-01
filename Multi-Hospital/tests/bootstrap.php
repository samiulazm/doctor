<?php

declare(strict_types=1);

$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (!is_file($autoload)) {
    fwrite(STDERR, "Run `composer install` in Multi-Hospital first.\n");
    exit(1);
}

require $autoload;
