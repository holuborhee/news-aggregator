<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ElasticSearchService;
use App\Jobs\ScrapeNewsJob;

class SetupNewsAggregator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-news-aggregator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('🔧 Creating indexes in Elasticsearch...');
        app(ElasticSearchService::class)->setupIndexes();

        $this->info('📦 Seeding meta index (e.g. last scrape time)...');
        app(ElasticSearchService::class)->seedMeta();

        $this->info('🚀 Dispatching initial news scraping...');
        ScrapeNewsJob::dispatch();

        $this->info('✅ Done! You may now start scheduler with:');
        $this->line('   docker exec -it laravel_app php artisan schedule:work');
    }
}
