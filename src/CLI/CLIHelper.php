<?php

namespace RhDevelopment\GoproFileConverter\CLI;

class CLIHelper
{
    /**
     * @param string $message
     * @return void
     */
    public static function output(string $message): void
    {
        echo '[[' . date('Y-m-d H:i:s') . ']] - ' . $message . PHP_EOL;
    }
}
