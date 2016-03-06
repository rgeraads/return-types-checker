<?php

namespace ReturnTypesChecker\Reflector;

final class ReflectedClass
{
    /**
     * @var string The name of the class.
     */
    private $name;

    /**
     * @var int Line number that indicates the beginning of the class.
     */
    private $startLine;

    /**
     * @var int Line number that indicates the end of the class.
     */
    private $endLine;

    /**
     * @var array Actual class contents parsed as an array.
     */
    private $contents;

    private function __construct(string $name, int $startLine, int $endLine, array $contents)
    {
        $this->name      = $name;
        $this->startLine = $startLine;
        $this->endLine   = $endLine;
        $this->contents  = $contents;
    }

    public static function generate(\ReflectionClass $reflectionClass): self
    {
        $contents = explode(PHP_EOL, file_get_contents($reflectionClass->getFileName()));

        foreach ($contents as $key => $row) {
            if ($key < $reflectionClass->getStartLine() - 1 || $key > $reflectionClass->getEndLine() - 1) {
                unset($contents[$key]);

                continue;
            }
        }

        return new self(
            $reflectionClass->getName(),
            $reflectionClass->getStartLine(),
            $reflectionClass->getEndLine(),
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
