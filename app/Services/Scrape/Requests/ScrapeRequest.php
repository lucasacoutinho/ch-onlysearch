<?php

namespace App\Services\Scrape\Requests;

use App\Data\Scrape\ScrapeDTO;
use App\Services\Scrape\Pages\Page;
use Throwable;

interface ScrapeRequest
{
    public function getPage(): Page;

    public function processing(): void;

    public function completed(ScrapeDTO $result): void;

    public function failed(Throwable $e): void;
}
