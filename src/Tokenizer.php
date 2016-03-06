<?php

namespace ReturnTypesChecker;

use ReturnTypesChecker\Tokenizer\TokenizedClass;

final class Tokenizer
{
    private $tokenizedFile;

    private function __construct(array $tokenizedFile)
    {
        $this->tokenizedFile = $tokenizedFile;
    }

    public static function tokenize(string $filePath): self
    {
        $tokenizedFile = self::tokenizeFile($filePath);

        return new self($tokenizedFile);
    }

    private static function tokenizeFile(string $filePath): array
    {
        return token_get_all(file_get_contents($filePath));
    }

    public function retrieveClasses()
    {
        $class = TokenizedClass::generate($this->tokenizedFile);
var_dump($class);
        $reflectionClass = new \ReflectionClass($class->getName());
        $tokenizedClass  = TokenizedClass::generate($reflectionClass);
    }

    private function getClassesFromFile(array $tokens): array
    {
        $classes = [];

        for ($i = 0; $i < count($tokens); $i++) {
            if ($tokens[$i][0] === T_CLASS) {
                $classes[] = $tokens[$i + 2][1];
            }
        }

        return $classes;
    }

    private function getFunctionsFromFile(array $tokens): array
    {
        $methods = [];

        for ($i = 0; $i < count($tokens); $i++) {
            if ($tokens[$i][0] === T_FUNCTION) {
                $methods[] = $tokens[$i + 2][1];
            }
            if ($tokens[$i][0] === T_CALLABLE) {
                var_dump($tokens[$i][0]);
            }
        }

        return $methods;
    }
}
