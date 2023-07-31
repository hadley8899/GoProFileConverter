<?php

use RhDevelopment\GoproFileConverter\Converter\FileConverter;

require __DIR__ . '/vendor/autoload.php';

if (!isset($argv[1])) {
    echo 'Please provide a directory to convert, e.g. php run.php /path/to/directory' . PHP_EOL;
    exit;
}

$dir = trim($argv[1]);

FileConverter::convertNames($dir);