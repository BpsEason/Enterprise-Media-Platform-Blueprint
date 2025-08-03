<?php

namespace App\Providers;

use App\Application\ArticleService;
use App\Application\IArticleService;
use App\Console\Commands\SetupElasticsearchCommand;
use App\Console\Commands\SetupQueueCommand;
use App\Domain\IArticleRepository;
use App\Infrastructure\Persistence\BigQueryArticleRepository;
use App\Infrastructure\Persistence\ElasticsearchArticleRepository;
use App\Infrastructure\Persistence\PostgresArticleRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(IArticleRepository::class, function ($app) {
            return match (env('DB_CONNECTION')) {
                'pgsql' => new PostgresArticleRepository(),
                'elasticsearch' => new ElasticsearchArticleRepository(),
                'bigquery' => new BigQueryArticleRepository(),
                default => new PostgresArticleRepository(),
            };
        });

        $this->app->bind(IArticleService::class, ArticleService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 註冊自訂 Artisan 命令
        if ($this->app->runningInConsole()) {
            $this->commands([
                SetupQueueCommand::class,
                SetupElasticsearchCommand::class,
            ]);
        }
    }
}
