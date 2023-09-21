<?php

use RhDevelopment\GoproFileConverter\CLI\CLIHelper;
use RhDevelopment\GoproFileConverter\Converter\FileConverter;

require __DIR__ . '/vendor/autoload.php';

if (!isset($argv[1])) {
    CLIHelper::output('Please provide a directory to convert, e.g. php run.php /path/to/directory');
    exit;
}

$dir = trim($argv[1]);

FileConverter::convertNames($dir);
