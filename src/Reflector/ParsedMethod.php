<?php

namespace ReturnTypesChecker\Reflector;

final class ParsedMethod
{
    const EXCLUDED_METHODS = ['__construct', '__destruct', '__clone'];

    /**
     * @var string The name of the method.
     */
    private $name;

    /**
     * @var int Line number that indicates the beginning of the method.
     */
    private $startLine;

    /**
     * @var int Line number that indicates the end of the method.
     */
    private $endLine;

    /**
     * @var array Actual method contents parsed as an array.
     */
    private $contents;

    private function __construct(string $name, int $startLine, int $endLine, array $contents)
    {
        $this->name      = $name;
        $this->startLine = $startLine;
        $this->endLine   = $endLine;
        $this->contents  = $contents;
    }

    public static function generate(\ReflectionMethod $reflectionMethod): self
    {
        $contents = explode(PHP_EOL, file_get_contents($reflectionMethod->getFileName()));

        foreach ($contents as $key => $row) {
            if ($key < $reflectionMethod->getStartLine() - 1 || $key > $reflectionMethod->getEndLine() - 1) {
                unset($contents[$key]);

                continue;
            }
        }

        return new self(
            $reflectionMethod->getName(),
            $reflectionMethod->getStartLine(),
            $reflectionMethod->getEndLine(),
            $contents
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStartLine(): int
    {
        return $this->startLine;
    }

    public function getEndLine(): int
    {
        return $this->endLine;
    }

    public function getContents(): array
    {
        return $this->contents;
    }
}
