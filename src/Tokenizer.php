<?php

namespace ReturnTypesChecker;

use ReturnTypesChecker\Tokenizer\TokenizedClass;
use ReturnTypesChecker\Tokenizer\TokenizedFunction;

final class Tokenizer
{
    private $classes   = [];
    private $methods   = [];
    private $functions = [];

    private function __construct(array $tokenizedFile)
    {
        $this->retrieveClassesFromFile($tokenizedFile);
        $this->retrieveFunctionsFromFile($tokenizedFile);
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

    private function retrieveClassesFromFile(array $tokenizedFile)
    {
        for ($i = 0; $i < count($tokenizedFile); $i++) {
            if ($tokenizedFile[$i][0] === T_CLASS) {
                $this->classes[] = TokenizedClass::generate($tokenizedFile[$i + 2]);
            }
        }
    }

    private function retrieveFunctionsFromFile(array $tokenizedFile)
    {
        for ($i = 0; $i < count($tokenizedFile); $i++) {
            if ($tokenizedFile[$i][0] === T_FUNCTION) {
                $this->functions[] = TokenizedFunction::generate($tokenizedFile[$i + 2]);
            }
        }
    }

    public function getClasses(): array
    {
        return $this->classes;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getFunctions(): array
    {
        return $this->functions;
    }
}
