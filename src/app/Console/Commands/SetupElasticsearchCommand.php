<?php

namespace App\Console\Commands;

use Elasticsearch\ClientBuilder;
use Illuminate\Console\Command;

class SetupElasticsearchCommand extends Command
{
    protected $signature = 'elasticsearch:setup';
    protected $description = 'Setup Elasticsearch indices';

    public function handle()
    {
        $this->info('Connecting to Elasticsearch...');
        try {
            $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST')])->build();
            $params = [
                'index' => 'articles',
                'body' => [
                    'mappings' => [
                        'properties' => [
                            'title' => ['type' => 'text'],
                            'content' => ['type' => 'text'],
                            'status' => ['type' => 'keyword'],
                        ]
                    ]
                ]
            ];
            
            if ($client->indices()->exists(['index' => 'articles'])) {
                $this->info('Index "articles" already exists. Skipping creation.');
            } else {
                $client->indices()->create($params);
                $this->info('Elasticsearch index "articles" created successfully.');
            }
        } catch (\Exception $e) {
            $this->error('Failed to connect to Elasticsearch or create index: ' . $e->getMessage());
        }
    }
}
