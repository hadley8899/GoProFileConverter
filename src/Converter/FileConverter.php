<?php

namespace RhDevelopment\GoproFileConverter\Converter;

use RhDevelopment\GoproFileConverter\CLI\CLIFuncs;

class FileConverter
{
    public static function convertNames(string $directory): void
    {
        if (!is_dir($directory)) {
            CLIFuncs::output('Directory does not exist, Please enter a valid directory');
            exit;
        }
        CLIFuncs::output('Converting files in ' . $directory);

        $files = self::removeNonGoProFiles(scandir($directory));

        CLIFuncs::output('Found ' . count($files) . ' files to convert');

        self::convertFiles($files, $directory);
    }

    private static function convertFiles(array $files, string $directory): void
    {
        foreach ($files as $file) {
            // First get the file details
            $fileDetails = self::getFileDetails($directory, $file);

            $originalExtension = $fileDetails['extension'];

            if (empty($originalExtension)) {
                CLIFuncs::output('File ' . $file . ' has no extension, skipping');
                continue;
            }

            $originalName = $fileDetails['filename'];

            // check if the filename follows GoPro naming convention
            if (preg_match('/^..(\d{2})(\d{4})$/', $originalName, $matches)) {
                // Matches the GoPro naming convention
                $groupNumber = $matches[1];
                $fileNumber = $matches[2];

                // format new name
                $newName = self::newName($groupNumber, $fileNumber);

                // rename the file
                rename($directory . '/' . $file, $directory . '/' . $newName . '.' . $originalExtension);
            } else {
                CLIFuncs::output('File ' . $file . ' does not match GoPro naming convention, skipping');
            }
        }
    }

    private static function getFileDetails(string $directory, string $file): array|string
    {
        return pathinfo($directory . '/' . $file);
    }

    private static function newName($groupNumber, $fileNumber): string
    {
        return $fileNumber . ' - ' . $groupNumber;
    }

    private static function removeNonGoProFiles(array $files): array
    {
        // Remove the directories
        $files = array_diff($files, ['.', '..']);
        $files = self::removeNonVideoFiles($files);
        return self::removeNonGoProLikeFiles($files);
    }

    private static function removeNonVideoFiles(array $files): array
    {
        // Remove any files that are not MP4 or m4v
        return array_filter($files, function ($file) {
            return preg_match('/\.m4v$|\.MP4$/', $file);
        });
    }

    private static function removeNonGoProLikeFiles(array $files): array
    {
        return array_filter(array_map(function ($file) {

            // Remove the extension from the file
            $testFile = self::removeExtension($file);

            if (strlen($testFile) !== 8) {
                return null;
            }

            return $file;
        }, $files));
    }

    private static function removeExtension($filename): array|string
    {
        return pathinfo($filename, PATHINFO_FILENAME);
    }
}