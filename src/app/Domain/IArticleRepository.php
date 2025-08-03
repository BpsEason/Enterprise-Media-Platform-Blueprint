<?php

namespace App\Domain;

use App\ValueObjects\ArticleId;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface IArticleRepository
{
    public function findAll(): LengthAwarePaginator;
    public function findById(ArticleId $id): ?Article;
    public function save(Article $article): void;
    public function searchByTitleOrContent(string $query): LengthAwarePaginator;
}
