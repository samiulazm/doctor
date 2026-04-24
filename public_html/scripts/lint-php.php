<?php

declare(strict_types=1);

/**
 * Syntax-check PHP files without spawning php -l per file (fast on large trees).
 * Scans: index.php, application/ (excludes third_party, cache).
 */

$root = dirname(__DIR__);

// PHP 8.5+ may emit E_DEPRECATED while parsing (e.g. legacy (integer) casts in source files).
$previousErrorReporting = error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

$files = [];
if (is_file($root . '/index.php')) {
    $files[] = $root . '/index.php';
}

$app = $root . '/application';
if (is_dir($app)) {
    // third_party, cache: excluded as before.
    // PHPExcel, Zend: legacy bundled libs; PHP 8 removed brace offset syntax ($str{0}) so
    // real php -l fails on those trees; token_get_all(TOKEN_PARSE) on PHP 8.5+ also flags them.
    $exclude = '#[/\\\\](third_party|cache)[/\\\\]#';
    $excludeLegacyLib = '#[/\\\\]libraries[/\\\\](PHPExcel|Zend)[/\\\\]#';
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($app, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
            continue;
        }
        $path = $file->getPathname();
        if (preg_match($exclude, $path) || preg_match($excludeLegacyLib, $path)) {
            continue;
        }
        $files[] = $path;
    }
}

$errors = [];
$count = 0;

foreach ($files as $path) {
    $count++;
    $code = @file_get_contents($path);
    if ($code === false) {
        $errors[] = "Cannot read: {$path}";
        continue;
    }
    try {
        token_get_all($code, TOKEN_PARSE);
    } catch (ParseError $e) {
        $errors[] = sprintf('%s:%d: %s', $path, $e->getLine(), $e->getMessage());
    }
}

error_reporting($previousErrorReporting);

fwrite(STDOUT, "Linted {$count} PHP files (application + index.php).\n");

if ($errors !== []) {
    fwrite(STDERR, implode(PHP_EOL, $errors) . PHP_EOL);
    exit(1);
}

exit(0);
