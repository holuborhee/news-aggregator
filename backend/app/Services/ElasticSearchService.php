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

    public function search($index, $query)
    {
        $params = [
            'index' => $index,
            'body' => $query,
        ];

        $result = $this->es->search($params);
        return [
            'articles' => collect($result['hits']['hits'])->pluck('_source')->toArray(),
            'total' => $result['hits']['total']['value'] ?? 0,
        ];
    }

    public function get($index, $id)
    {
        try {
            $response = $this->es->get([
                'index' => $index,
                'id' => $id,
            ]);
            return $response->asArray();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getPreference($userId)
    {
        try {
            $response = $this->es->get([
                'index' => 'user_preferences',
                'id'    => "user_{$userId}",
            ]);

            return $response['_source'] ?? null;

        } catch (\Exception $e) {
            return null; // not found or error
        }
    }

    public function savePreference($userId, array $data)
    {
        return $this->es->index([
            'index' => 'user_preferences',
            'id'    => "user_{$userId}",
            'body'  => [
                'categories' => $data['categories'] ?? [],
                'sources'    => $data['sources'] ?? [],
                'authors'    => $data['authors'] ?? [],
                'updated_at' => now()->toIso8601String(),
            ]
        ]);
    }

    public function getAllSources()
    {
        $response = $this->es->search([
            'index' => 'articles',
            'size'  => 0,
            'body'  => [
                'aggs' => [
                    'sources' => [
                        'terms' => [
                            'field' => 'source.slug',
                            'size'  => 1000,
                        ],
                        'aggs' => [
                            'source_name' => [
                                'top_hits' => [
                                    '_source' => ['source.name'],
                                    'size' => 1,
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        return collect($response['aggregations']['sources']['buckets'])->map(function ($bucket) {
            return [
                'slug' => $bucket['key'],
                'name' => $bucket['source_name']['hits']['hits'][0]['_source']['source']['name'] ?? $bucket['key'],
            ];
        })->values();
    }

    public function getAllAuthors()
    {
        $response = $this->es->search([
            'index' => 'articles',
            'size'  => 0,
            'body'  => [
                'aggs' => [
                    'authors' => [
                        'terms' => [
                            'field' => 'author.slug',
                            'size'  => 1000,
                        ],
                        'aggs' => [
                            'author_name' => [
                                'top_hits' => [
                                    '_source' => ['author.name'],
                                    'size' => 1,
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        return collect($response['aggregations']['authors']['buckets'])->map(function ($bucket) {
            return [
                'slug' => $bucket['key'],
                'name' => $bucket['author_name']['hits']['hits'][0]['_source']['author']['name'] ?? $bucket['key'],
            ];
        })->values();
    }

}
