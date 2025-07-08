<?php

namespace App\Services;

use Elastic\Elasticsearch\Client;
use Carbon\Carbon;

class ElasticSearchService
{
    protected Client $es;

    public function __construct(Client $es)
    {
        $this->es = $es;
    }

    public function setupIndexes(): void
    {
        $this->setupIndex('articles', [
            'mappings' => [
                'properties' => [
                    'title'        => ['type' => 'text'],
                    'description'  => ['type' => 'text'],
                    'content'      => ['type' => 'text'],
                    'url'          => ['type' => 'keyword'],
                    'image_url'    => ['type' => 'keyword'],
                    'published_at' => ['type' => 'date'],
                    'scraped_at'   => ['type' => 'date'],
                    'source_api'   => ['type' => 'keyword'],

                    'source' => [
                        'properties' => [
                            'slug' => ['type' => 'keyword'],
                            'name' => ['type' => 'text'],
                        ]
                    ],

                    'author' => [
                        'properties' => [
                            'slug'        => ['type' => 'keyword'],
                            'name'        => ['type' => 'text'],
                            'source_slug' => ['type' => 'keyword'],
                        ]
                    ],

                    'categories' => [
                        'type' => 'nested',
                        'properties' => [
                            'slug' => ['type' => 'keyword'],
                            'name' => ['type' => 'text'],
                        ]
                    ],
                ]
            ]
        ]);

        $this->setupIndex('user_preferences', [
            'mappings' => [
                'properties' => [
                    'user_id' => ['type' => 'keyword'],
                    'categories' => ['type' => 'keyword'],
                    'sources' => ['type' => 'keyword'],
                    'authors' => ['type' => 'keyword'],
                    'updated_at' => ['type' => 'date'],
                ]
            ]
        ]);

        $this->setupIndex('meta', [
            'mappings' => [
                'properties' => [
                    'key' => ['type' => 'keyword'],
                    'value' => ['type' => 'keyword'],
                ]
            ]
        ]);
    }

    protected function setupIndex(string $index, array $body): void
    {
        if (!$this->es->indices()->exists(['index' => $index])->asBool()) {
            $this->es->indices()->create([
                'index' => $index,
                'body' => $body
            ]);
        }
    }

    public function seedMeta(): void
    {
        $this->es->index([
            'index' => 'meta',
            'id' => 'last_scrape_time',
            'body' => [
                'key' => 'last_scrape_time',
                'value' => now()->subDays(7)->toIso8601String(),
            ]
        ]);
    }

    public function getLastScrapeTime(): Carbon
    {
        try {
            $response = $this->es->get([
                'index' => 'meta',
                'id' => 'last_scrape_time',
            ]);

            return Carbon::parse($response['_source']['value'] ?? now()->subDays(7));
        } catch (\Exception $e) {
            return now()->subDays(7);
        }
    }

    public function updateLastScrapeTime(Carbon $time): void
    {
        $this->es->index([
            'index' => 'meta',
            'id' => 'last_scrape_time',
            'body' => [
                'key' => 'last_scrape_time',
                'value' => $time->toIso8601String(),
            ]
        ]);
    }

    public function storeArticle(array $article): void
    {
        $id = md5($article['url']); // deterministic ID to prevent duplicates

        $this->es->index([
            'index' => 'articles',
            'id' => $id,
            'body' => $article
        ]);
    }
}
