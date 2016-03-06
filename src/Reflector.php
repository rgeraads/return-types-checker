<?php

namespace ReturnTypesChecker;

use ReturnTypesChecker\Reflector\ParsedClass;
use ReturnTypesChecker\Reflector\ParsedFunction;
use ReturnTypesChecker\Reflector\ParsedMethod;
use ReturnTypesChecker\Tokenizer\TokenizedClass;

final class Reflector
{
    /**
     * @var int Line number that indicates the beginning of the class.
     */
    private $classStartLine;

    /**
     * @var int Line number that indicates the end of the class.
     */
    private $classEndLine;

    public static function reflect(string $filePath)
    {
        $tokens = Tokenizer::tokenize($filePath);
        $class = $tokens->retrieveClasses();
        self::parseClass();
    }

    public function checkIfMethodHasReturType(\ReflectionMethod $method)
    {
        $returnValue = $this->getReturnValue($this->parseMethod($method));

        $reflectionMethod = new \ReflectionMethod($method->getDeclaringClass()->getName(), $method->getName());

        if ($returnValue !== self::NO_RETURN_VALUE && $reflectionMethod->getReturnType() === null) {
            echo sprintf(
                    'Method "%s" returns %s but has no return type set.', $method->getName(), $returnValue
                ) . PHP_EOL;
        }
    }

    private static function parseClass(\ReflectionClass $method): ParsedClass
    {
        return ParsedClass::generate($method);
    }

    private static function parseMethod(\ReflectionMethod $method): ParsedMethod
    {
        return ParsedMethod::generate($method);
    }

    private static function parseFunction(\ReflectionFunction $function): ParsedFunction
    {
        return ParsedFunction::generate($function);
    }
}
