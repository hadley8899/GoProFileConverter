<?php

namespace RhDevelopment\GoproFileConverter\CLI;

class CLIFuncs
{
    public static function output($message)
    {
        echo '[[' . date('Y-m-d H:i:s') . ']] - ' . $message . PHP_EOL;
    }
}