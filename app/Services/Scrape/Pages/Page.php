<?php

namespace App\Services\Scrape\Pages;

use App\Data\Scrape\ScrapeDTO;

interface Page
{
    public function getUrl(): string;

    /**
     * @return callable(string $html): ScrapeDTO
     */
    public function getHandler(): callable;
}
