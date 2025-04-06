<?php

namespace App\Services\Scrape;

use App\Data\Scrape\ScrapeDTO;
use App\Services\Scrape\Pages\Page;
use Spatie\Browsershot\Browsershot;

class ScrapeService
{
    protected Page $page;

    public function __construct(
        protected Browsershot $browsershot,
    ) {}

    public function setPage(Page $page): void
    {
        $this->page = $page;
    }

    public function scrape(): ScrapeDTO
    {
        if (! $this->page) {
            throw new \Exception('Page not set');
        }

        $this->browsershot->url($this->page->getUrl());
        $this->browsershot->noSandbox();
        $this->browsershot->waitUntilNetworkIdle();
        $html = $this->browsershot->bodyHtml();

        return $this->handle($html);
    }

    private function handle(string $content): ScrapeDTO
    {
        $handler = $this->page->getHandler();
        return $handler($content);
    }
}
