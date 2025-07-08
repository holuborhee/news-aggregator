<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\ElasticSearchService;
use App\Services\NewsApiService;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class ScrapeNewsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $esService = app(ElasticSearchService::class);
        $newsService = app(NewsApiService::class);

        $lastScrape = $esService->getLastScrapeTime();
        $now = Carbon::now();

        logger("🔄 Fetching articles from {$lastScrape->toIso8601String()} to {$now->toIso8601String()}");

        $articles = $newsService->fetchArticles($lastScrape, $now);

        foreach ($articles as $article) {
            $esService->storeArticle($article);
        }

        $esService->updateLastScrapeTime($now);

        logger("✅ Scraped and stored " . count($articles) . " articles.");
    }
}
