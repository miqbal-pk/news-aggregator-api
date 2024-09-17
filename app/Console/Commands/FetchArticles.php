<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ArticlesService;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command will fetch articles from different sources';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ArticlesService::fetchArticles();
    }
}
