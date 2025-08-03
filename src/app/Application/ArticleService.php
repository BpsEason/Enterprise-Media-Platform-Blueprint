<?php

namespace App\Application;

use App\Domain\Article;
use App\Domain\Events\ArticleCreated;
use App\Domain\IArticleRepository;
use App\ValueObjects\ArticleId;
use App\ValueObjects\CreateArticleDto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

class ArticleService implements IArticleService
{
    private IArticleRepository $articleRepository;

    public function __construct(IArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function getAllArticles(): Collection
    {
        return $this->articleRepository->findAll();
    }

    public function getArticleById(string $id): ?Article
    {
        return $this->articleRepository->findById(new ArticleId($id));
    }

    public function createArticle(CreateArticleDto $dto): Article
    {
        $article = Article::create($dto->title, $dto->content);
        $this->articleRepository->save($article);

        // 觸發領域事件
        Event::dispatch(new ArticleCreated($article));

        return $article;
    }

    public function searchArticles(string $query): Collection
    {
        return $this->articleRepository->searchByTitleOrContent($query);
    }
}
