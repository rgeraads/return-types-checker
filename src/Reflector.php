<?php

namespace ReturnTypesChecker;

use ReturnTypesChecker\Reflector\ReflectedClass;
use ReturnTypesChecker\Reflector\ReflectedFunction;
use ReturnTypesChecker\Reflector\ReflectedMethod;

final class Reflector
{
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

    private function __construct(array $classes, array $methods, array $functions)
    {
        $this->classes   = $classes;
        $this->methods   = $methods;
        $this->functions = $functions;
    }

    public static function reflect(string $filePath)
    {
        $tokenizedFile = Tokenizer::tokenize($filePath);

        // todo: loop over file and generate reflected classes, methods & functions.

        $classes   = [];
        $methods   = [];
        $functions = [];

        return new self($classes, $methods, $functions);
    }

    private static function reflectClass(\ReflectionClass $class): ReflectedClass
    {
        return ReflectedClass::generate($class);
    }

    private static function reflectMethod(\ReflectionMethod $method): ReflectedMethod
    {
        return ReflectedMethod::generate($method);
    }

    private static function reflectFunction(\ReflectionFunction $function): ReflectedFunction
    {
        return ReflectedFunction::generate($function);
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
