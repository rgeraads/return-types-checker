<?php

namespace ReturnTypesChecker\Tokenizer;

final class TokenizedClass
{
    /**
     * @var string The name of the class.
     */
    private $name;

    /**
     * @var int Line number that indicates the beginning of the class.
     */
    private $startLine;

    private function __construct(string $name, string $startLine)
    {
        $this->name      = $name;
        $this->startLine = $startLine;
    }

    public static function generate(array $tokens): self
    {
        for ($i = 0; $i < count($tokens); $i++) {
            if ($tokens[$i][0] === T_CLASS) {
                $name      = $tokens[$i + 2][1];
                $startLine = (int) $tokens[$i + 2][2];

                return new self($name, $startLine);
            }
        }

        // todo: what to do when no class is found?
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
