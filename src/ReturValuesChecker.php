<?php

namespace ReturnTypesChecker;

declare(strict_types = 1);

final class ReturValuesChecker
{
    const NO_RETURN_VALUE = 'void';

    /**
     * @var int Line number that indicates the beginning of the class.
     */
    private $classStartLine;

    /**
     * @var int Line number that indicates the end of the class.
     */
    private $classEndLine;

    /**
     * @var int Line number that indicates the beginning of the method.
     */
    private $methodStartLine;

    /**
     * @var int Line number that indicates the end of the method.
     */
    private $methodEndLine;

    public function scan(string $path)
    {
        $filePaths = FilesGatherer::findPhpFiles($path);
        $this->checkReturnTypes($filePaths);
    }

    private function checkReturnTypes($pathNames)
    {
        foreach ($pathNames as $pathName) {
            $tokens    = $this->tokenizeFile($pathName);
            $classes   = $this->getClassesFromFile($tokens);
            $functions = $this->getFunctionsFromFile($tokens);

            if ($classes === []) {
                echo sprintf('No classes found in "%s"', $pathName) . PHP_EOL;

                continue;
            }

            $reflectionClass      = new \ReflectionClass($classes[0]);
            $this->classStartLine = $reflectionClass->getStartLine();
            $this->classEndLine   = $reflectionClass->getEndLine();
            $methods              = $reflectionClass->getMethods();

            foreach ($methods as $method) {
                $this->checkIfMethodHasReturType($method);
            }
        }
    }

    private function checkIfMethodHasReturType(\ReflectionMethod $method)
    {
        if ($method->getName() === '__construct') {
            return;
        }

        $returnValue = $this->getReturnValue($this->parseMethod($method));

        $reflectionMethod = new \ReflectionMethod($method->getDeclaringClass()->getName(), $method->getName());

        if ($returnValue !== self::NO_RETURN_VALUE && $reflectionMethod->getReturnType() === null) {
            echo sprintf(
                    'Method "%s" returns %s but has no return type set.', $method->getName(), $returnValue
                ) . PHP_EOL;
        }
    }

    private function parseMethod(\ReflectionMethod $method): array
    {
        $file = explode(PHP_EOL, file_get_contents($method->getFileName()));

        $reflectionMethod      = new \ReflectionMethod($method->getDeclaringClass()->getName(), $method->getName());
        $this->methodStartLine = $reflectionMethod->getStartLine();
        $this->methodEndLine   = $reflectionMethod->getEndLine();

        foreach ($file as $key => $row) {
            if ($key < $this->methodStartLine - 1 || $key > $this->methodEndLine - 1) {
                unset($file[$key]);

                continue;
            }
        }

        return $file;
    }

    private function getReturnValue(array $parsedMethod): string
    {
        foreach ($parsedMethod as $key => $row) {
            if ($pos = strpos(strtolower(ltrim($row)), 'return;') !== false) {
                return self::NO_RETURN_VALUE;
            }

            if ($pos = strpos(strtolower(ltrim($row)), 'return') !== false) {
                $returnValue = substr(ltrim($row), $pos + 6);

                return ltrim($this->stripSemicolon($returnValue));
            }
        }

        if (!isset($returnValue)) {
            return self::NO_RETURN_VALUE;
        }
    }

    private function stripSemicolon($returnValue): string
    {
        if (strpos($returnValue, ';', strlen($returnValue) - 1) !== false) {
            $returnValue = substr($returnValue, 0, strlen($returnValue) - 1);
        }

        return $returnValue;
    }

    private function tokenizeFile(string $filepath): array
    {
        return token_get_all(file_get_contents($filepath));
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
