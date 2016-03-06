<?php

namespace ReturnTypesChecker;

final class App
{
    public static function scan(string $path)
    {
        $filePaths = FilesGatherer::findPhpFiles($path);

        foreach ($filePaths as $filePath) {
            Reflector::reflect($filePath);
        }
    }
}
