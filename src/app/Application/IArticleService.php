<?php

namespace App\Application;

use App\Domain\Article;
use App\ValueObjects\CreateArticleDto;
use Illuminate\Support\Collection;

interface IArticleService
{
    public function getAllArticles(): Collection;
    public function getArticleById(string $id): ?Article;
    public function createArticle(CreateArticleDto $dto): Article;
    public function searchArticles(string $query): Collection;
}
