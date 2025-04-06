<?php

namespace App\Jobs;

use App\Services\Scrape\Requests\ScrapeRequest;
use App\Services\Scrape\ScrapeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class Scrape implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public ScrapeRequest $request,
    ) {}

    public function handle(ScrapeService $scrapeService): void
    {
        $this->request->processing();

        $scrapeService->setPage($this->request->getPage());
        $result = $scrapeService->scrape();

        $this->request->completed($result);
    }

    public function failed(Throwable $e): void
    {
        $this->request->failed($e);
    }
}
