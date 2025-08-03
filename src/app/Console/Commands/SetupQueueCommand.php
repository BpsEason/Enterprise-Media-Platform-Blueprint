<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class SetupQueueCommand extends Command
{
    protected $signature = 'queue:setup';
    protected $description = 'Setup RabbitMQ queues';

    public function handle()
    {
        $this->info('Connecting to RabbitMQ...');
        try {
            $connection = new AMQPStreamConnection(
                env('RABBITMQ_HOST'),
                env('RABBITMQ_PORT'),
                env('RABBITMQ_USER'),
                env('RABBITMQ_PASSWORD'),
                env('RABBITMQ_VHOST')
            );
            $channel = $connection->channel();
            $channel->queue_declare('article_events', false, true, false, false);
            $this->info('Queue "article_events" created successfully.');
            $channel->close();
            $connection->close();
        } catch (\Exception $e) {
            $this->error('Failed to connect to RabbitMQ or create queue: ' . $e->getMessage());
        }
    }
}
