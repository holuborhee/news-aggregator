<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Category;

class NewsApiService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = 'https://newsapi.org/v2';
        $this->apiKey = config('services.newsapi.key');
    }

    public function fetchArticles(Carbon $from, Carbon $to): array
    {
        $categories = Category::all(); // Assuming categories are stored in DB

        return collect($categories)
            ->flatMap(function ($category) use ($from, $to) {
                $response = Http::get("{$this->baseUrl}/everything", [
                    'apiKey' => $this->apiKey,
                    'q' => $category->slug,
                    'from' => $from->toIso8601String(),
                    'to' => $to->toIso8601String(),
                    'language' => 'en',
                    'pageSize' => 100,
                ]);

                if (!$response->ok()) return [];

                return collect($response->json('articles'))->map(function ($article) use ($category) {
                    $sourceName = $article['source']['name'] ?? 'unknown-source';
                    $sourceSlug = Str::slug($sourceName);

                    $authorName = $article['author'] ?? null;
                    $authorSlug = $authorName
                        ? $sourceSlug . '__' . Str::slug($authorName)
                        : null;

                    return [
                        'title'        => $article['title'] ?? '',
                        'description'  => $article['description'] ?? '',
                        'content'      => $article['content'] ?? '',
                        'url'          => $article['url'] ?? '',
                        'image_url'    => $article['urlToImage'] ?? '',
                        'published_at' => isset($article['publishedAt']) ? Carbon::parse($article['publishedAt'])->toIso8601String() : null,
                        'scraped_at'   => now()->toIso8601String(),
                        'source_api'   => 'newsapi',

                        'source' => [
                            'slug' => $sourceSlug,
                            'name' => $sourceName,
                        ],

                        'author' => $authorName ? [
                            'slug'        => $authorSlug,
                            'name'        => $authorName,
                            'source_slug' => $sourceSlug,
                        ] : null,

                        'categories' => [[
                            'slug' => $category->slug,
                            'name' => $category->name,
                        ]],
                    ];
                });
            })
            ->values()
            ->toArray();
    }
}

