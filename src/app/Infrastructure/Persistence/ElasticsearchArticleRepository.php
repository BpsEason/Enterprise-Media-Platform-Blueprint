<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Article;
use App\Domain\IArticleRepository;
use App\ValueObjects\ArticleId;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as BasePaginator;

class ElasticsearchArticleRepository implements IArticleRepository
{
    private $client;
    private string $index = 'articles';

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST')])
            ->build();
    }

    public function findAll(): LengthAwarePaginator
    {
        $params = ['index' => $this->index, 'body' => ['query' => ['match_all' => new \stdClass()]]];
        $response = $this->client->search($params);

        $total = $response['hits']['total']['value'];
        $items = collect($response['hits']['hits'])->map(function ($hit) {
            return $this->toDomain($hit['_source']);
        });

        return new BasePaginator($items, $total, 15);
    }

    public function findById(ArticleId $id): ?Article
    {
        try {
            $response = $this->client->get([
                'index' => $this->index,
                'id'    => $id->toString()
            ]);
            return $this->toDomain($response['_source']);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function save(Article $article): void
    {
        $params = [
            'index' => $this->index,
            'id'    => $article->id->toString(),
            'body'  => $article->toArray()
        ];
        $this->client->index($params);
    }

    public function searchByTitleOrContent(string $query): LengthAwarePaginator
    {
        $params = [
            'index' => $this->index,
            'body'  => [
                'query' => [
                    'multi_match' => [
                        'query'  => $query,
                        'fields' => ['title', 'content']
                    ]
                ]
            ]
        ];
        $response = $this->client->search($params);

        $total = $response['hits']['total']['value'];
        $items = collect($response['hits']['hits'])->map(function ($hit) {
            return $this->toDomain($hit['_source']);
        });

        return new BasePaginator($items, $total, 15);
    }

    private function toDomain(array $data): Article
    {
        return Article::fromArray($data);
    }
}
