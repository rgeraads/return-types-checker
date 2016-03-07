<?php

namespace ReturnTypesChecker;

use ReturnTypesChecker\Tokenizer\TokenizedClass;
use ReturnTypesChecker\Tokenizer\TokenizedFunction;

final class Tokenizer
{
    private $filePath;

    /**
     * @var TokenizedClass[]
     */
    private $classes;

    /**
     * @var TokenizedMethod[]
     */
    private $methods;
    /**
     * @var TokenizedFunction[]
     */
    private $functions;

    private function __construct(string $filePath, array $classes, array $methods, array $functions)
    {
        $this->filePath  = $filePath;
        $this->classes   = $classes;
        $this->methods   = $methods;
        $this->functions = $functions;
    }

    public static function tokenize(string $filePath): self
    {
        $tokenizedFile = self::tokenizeFile($filePath);

        $classes = self::retrieveClassesFromFile($tokenizedFile);

        $methods = [];
        foreach ($classes as $class) {
            $methods = self::retrieveMethodsFromClass($class, $tokenizedFile);
        }

        $functions = self::retrieveFunctionsFromFile($tokenizedFile);

        return new self($filePath, $classes, $methods, $functions);
    }

    private static function tokenizeFile(string $filePath): array
    {
        return token_get_all(file_get_contents($filePath));
    }

    private static function retrieveClassesFromFile(array $tokenizedFile): array
    {
        $classes = [];

        $namespace = self::retrieveNamespaceFromFile($tokenizedFile);

        for ($i = 0; $i < count($tokenizedFile); $i++) {
            if ($tokenizedFile[$i][0] === T_CLASS) {
                $classes[] = TokenizedClass::generate($tokenizedFile[$i + 2], $namespace);
            }
        }

        return $classes;
    }

    private static function retrieveMethodsFromClass(TokenizedClass $class, array $tokenizedFile)
    {
        $methods = [];

        for ($i = 0; $i < count($tokenizedFile); $i++) {
            if ($tokenizedFile[$i][0] === T_FUNCTION) {
                $methods[] = TokenizedFunction::generate($tokenizedFile[$i + 2]);
            }
        }

        return $methods;
    }

    private static function retrieveFunctionsFromFile(array $tokenizedFile)
    {
        $functions = [];

        for ($i = 0; $i < count($tokenizedFile); $i++) {
            if ($tokenizedFile[$i][0] === T_FUNCTION) {
                $functions[] = TokenizedFunction::generate($tokenizedFile[$i + 2]);
            }
        }

        return $functions;
    }

    private static function retrieveNamespaceFromFile(array $tokenizedFile): string
    {
        $namespace = '';

        for ($i = 0; $i < count($tokenizedFile); $i++) {
            if ($tokenizedFile[$i][0] === T_NAMESPACE) {
                for ($j = $i + 2; $j < count($tokenizedFile); $j++) {
                    if ($tokenizedFile[$j] === ';') {
                        break;
                    }

                    $namespace .= is_array($tokenizedFile[$j]) ? $tokenizedFile[$j][1] : $tokenizedFile[$j];
                }
            }
        }

        return $namespace;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @return TokenizedClass[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * @return TokenizedMethod[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @return TokenizedFunction[]
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }
}
