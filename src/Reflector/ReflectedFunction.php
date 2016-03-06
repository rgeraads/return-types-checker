<?php

namespace ReturnTypesChecker\Reflector;

final class ReflectedFunction
{
    /**
     * @var string The name of the function.
     */
    private $name;

    /**
     * @var int Line number that indicates the beginning of the function.
     */
    private $startLine;

    /**
     * @var int Line number that indicates the end of the function.
     */
    private $endLine;

    /**
     * @var array Actual function contents parsed as an array.
     */
    private $contents;

    private function __construct(string $name, int $startLine, int $endLine, array $contents)
    {
        $this->name      = $name;
        $this->startLine = $startLine;
        $this->endLine   = $endLine;
        $this->contents  = $contents;
    }

    public static function generate(\ReflectionFunction $reflectionFunction): self
    {
        $contents = explode(PHP_EOL, file_get_contents($reflectionFunction->getFileName()));

        foreach ($contents as $key => $row) {
            if ($key < $reflectionFunction->getStartLine() - 1 || $key > $reflectionFunction->getEndLine() - 1) {
                unset($contents[$key]);

                continue;
            }
        }

        return new self(
            $reflectionFunction->getName(),
            $reflectionFunction->getStartLine(),
            $reflectionFunction->getEndLine(),
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
