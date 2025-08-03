<?php

namespace App\ValueObjects;

use InvalidArgumentException;

class ArticleStatus
{
    private const DRAFT = 'draft';
    private const PUBLISHED = 'published';

    private string $status;

    private function __construct(string $status)
    {
        if (!in_array($status, [self::DRAFT, self::PUBLISHED])) {
            throw new InvalidArgumentException("Invalid article status: $status");
        }
        $this->status = $status;
    }

    public static function draft(): self
    {
        return new self(self::DRAFT);
    }

    public static function published(): self
    {
        return new self(self::PUBLISHED);
    }

    public function isDraft(): bool
    {
        return $this->status === self::DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status === self::PUBLISHED;
    }

    public function toString(): string
    {
        return $this->status;
    }
}
