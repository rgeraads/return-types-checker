<?php

namespace ReturnTypesChecker;

final class FilesGatherer
{
    public static function findPhpFiles(string $path): array
    {
        $pathNames = [];

        $it = new \RecursiveDirectoryIterator($path);
        $it = new \RecursiveIteratorIterator($it);
        $it = new \RegexIterator($it, '/\.php$/i');

        /** @var SplFileInfo[] $it */
        foreach ($it as $fi) {
            $pathName = $fi->getPathname();
            if ($pathName === false) {
                continue;
            }

            $pathNames[] = realpath($pathName);
        }

        return $pathNames;
    }
}
