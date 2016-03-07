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

    /**
     * @var string Possible namespace of the class.
     */
    private $namespace;

    private function __construct(string $name, int $startLine, string $namespace)
    {
        $this->name      = $name;
        $this->startLine = $startLine;
        $this->namespace = $namespace;
    }

    public static function generate(array $token, string $namespace = ''): self
    {
        $name      = $token[1];
        $startLine = (int) $token[2];

        return new self($name, $startLine, $namespace);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStartLine(): int
    {
        return $this->startLine;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getFullyQualifiedName(): string
    {
        $fullyQualifiedName = '';

        if ($this->namespace !== '') {
            $fullyQualifiedName = $this->namespace . '\\';
        }

        return $fullyQualifiedName . $this->name;
    }
}
