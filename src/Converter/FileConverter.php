<?php

namespace RhDevelopment\GoproFileConverter\Converter;

use RhDevelopment\GoproFileConverter\CLI\CLIHelper;

class FileConverter
{
    /**
     * @param string $directory
     * @return void
     */
    public static function convertNames(string $directory): void
    {
        if (!is_dir($directory)) {
            CLIHelper::output('Directory does not exist, Please enter a valid directory');
            exit;
        }
        CLIHelper::output('Converting files in ' . $directory);

        $files = self::removeNonGoProFiles(scandir($directory));

        CLIHelper::output('Found ' . count($files) . ' files to convert');

        self::convertFiles($files, $directory);
    }

    /**
     * @param array $files
     * @param string $directory
     * @return void
     */
    private static function convertFiles(array $files, string $directory): void
    {
        foreach ($files as $file) {
            // First get the file details
            $fileDetails = self::getFileDetails($directory, $file);

            $originalExtension = $fileDetails['extension'];

            if (empty($originalExtension)) {
                CLIHelper::output('File ' . $file . ' has no extension, skipping');
                continue;
            }

            $originalName = $fileDetails['filename'];

            // check if the filename follows GoPro naming convention
            if (preg_match('/^..(\d{2})(\d{4})$/', $originalName, $matches)) {
                // Matches the GoPro naming convention
                [, $groupNumber, $fileNumber] = $matches;
                // format new name
                $newName = self::newName($groupNumber, $fileNumber);

                // rename the file
                rename($directory . '/' . $file, $directory . '/' . $newName . '.' . $originalExtension);
            } else {
                CLIHelper::output('File ' . $file . ' does not match GoPro naming convention, skipping');
            }
        }
    }

    /**
     * @param string $directory
     * @param string $file
     * @return array|string
     */
    private static function getFileDetails(string $directory, string $file): array|string
    {
        return pathinfo($directory . '/' . $file);
    }

    /**
     * @param string $groupNumber
     * @param string $fileNumber
     * @return string
     */
    private static function newName(string $groupNumber, string $fileNumber): string
    {
        return $fileNumber . ' - ' . $groupNumber;
    }

    /**
     * @param array $files
     * @return array
     */
    private static function removeNonGoProFiles(array $files): array
    {
        // Remove the directories
        $files = array_diff($files, ['.', '..']);
        $files = self::removeNonVideoFiles($files);
        return self::removeNonGoProLikeFiles($files);
    }

    /**
     * @param array $files
     * @return array
     */
    private static function removeNonVideoFiles(array $files): array
    {
        // Remove any files that are not MP4 or m4v
        return array_filter($files, static function ($file) {
            return preg_match('/\.m4v$|\.MP4$/', $file);
        });
    }

    /**
     * @param array $files
     * @return array
     */
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

    /**
     * @param string $filename
     * @return array|string
     */
    private static function removeExtension(string $filename): array|string
    {
        return pathinfo($filename, PATHINFO_FILENAME);
    }
}
