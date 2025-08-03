<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Article;
use App\Domain\IArticleRepository;
use App\Models\Article as ArticleModel;
use App\ValueObjects\ArticleId;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostgresArticleRepository implements IArticleRepository
{
    public function findAll(): LengthAwarePaginator
    {
        return ArticleModel::paginate(15);
    }

    public function findById(ArticleId $id): ?Article
    {
        $model = ArticleModel::find($id->toInt());
        return $model ? $this->toDomain($model) : null;
    }

    public function save(Article $article): void
    {
        $model = ArticleModel::find($article->id->toInt()) ?? new ArticleModel();
        $model->title = $article->title;
        $model->content = $article->content;
        $model->status = $article->status->toString();
        $model->save();

        if ($model->wasRecentlyCreated) {
            $article->id = new ArticleId($model->id);
        }
    }

    public function searchByTitleOrContent(string $query): LengthAwarePaginator
    {
        return ArticleModel::where('title', 'ilike', "%$query%")
                           ->orWhere('content', 'ilike', "%$query%")
                           ->paginate(15);
    }
    
    private function toDomain(ArticleModel $model): Article
    {
        return Article::fromArray($model->toArray());
    }
}
