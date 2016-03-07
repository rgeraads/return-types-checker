<?php

namespace ReturnTypesChecker;

use ReturnTypesChecker\Reflector\ReflectedClass;
use ReturnTypesChecker\Reflector\ReflectedFunction;
use ReturnTypesChecker\Reflector\ReflectedMethod;
use ReturnTypesChecker\Tokenizer\TokenizedClass;
use ReturnTypesChecker\Tokenizer\TokenizedFunction;

final class Reflector
{
    private $filePath;

    /**
     * @var ReflectedClass[]
     */
    private $classes;

    /**
     * @var ReflectedMethod[]
     */
    private $methods;
    /**
     * @var ReflectedFunction[]
     */
    private $functions;

    private function __construct(string $filePath, array $classes, array $methods, array $functions)
    {
        $this->filePath  = $filePath;
        $this->classes   = $classes;
        $this->methods   = $methods;
        $this->functions = $functions;
    }

    public static function reflect(string $filePath)
    {
        $tokenizedFile = Tokenizer::tokenize($filePath);

        $classes   = [];
        $methods   = [];
        $functions = [];

        foreach ($tokenizedFile->getClasses() as $class) {
            $classes[] = self::reflectClass($class);

            foreach ($tokenizedFile->getMethods() as $method) {
                $methods[] = self::reflectMethod($class, $method->getName());
            }
        }

        foreach ($tokenizedFile->getFunctions() as $function) {
//            $functions[] = self::reflectFunction($function);
        }

        return new self($filePath, $classes, $methods, $functions);
    }

    private static function reflectClass(TokenizedClass $class): ReflectedClass
    {
        return ReflectedClass::generate(new \ReflectionClass($class->getFullyQualifiedName()));
    }

    private static function reflectMethod(TokenizedClass $class, string $methodName): ReflectedMethod
    {
        return ReflectedMethod::generate(new \ReflectionMethod($class->getFullyQualifiedName(), $methodName));
    }

    private static function reflectFunction(TokenizedFunction $function): ReflectedFunction
    {
        return ReflectedFunction::generate(new \ReflectionFunction($function->getName()));
    }

    public function getFilePath(): string
    {
        return $this->filePath;
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
