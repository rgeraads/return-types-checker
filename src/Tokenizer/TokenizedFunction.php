<?php

namespace ReturnTypesChecker\Tokenizer;

final class TokenizedFunction
{
    /**
     * @var string The name of the function.
     */
    private $name;

    /**
     * @var int Line number that indicates the beginning of the function.
     */
    private $startLine;

    private function __construct(string $name, int $startLine)
    {
        $this->name      = $name;
        $this->startLine = $startLine;
    }

    public static function generate(array $token): self
    {
        $name      = $token[1];
        $startLine = (int) $token[2];

        return new self($name, $startLine);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStartLine(): int
    {
        return $this->startLine;
    }
}
