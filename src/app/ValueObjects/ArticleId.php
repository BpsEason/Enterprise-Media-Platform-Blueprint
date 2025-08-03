<?php

namespace App\ValueObjects;

use InvalidArgumentException;

class ArticleId
{
    private ?int $id;

    public function __construct(?int $id)
    {
        if ($id !== null && $id <= 0) {
            throw new InvalidArgumentException("Article ID must be a positive integer.");
        }
        $this->id = $id;
    }

    public function toInt(): ?int
    {
        return $this->id;
    }

    public function toString(): string
    {
        return (string) $this->id;
    }
}
