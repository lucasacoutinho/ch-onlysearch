<?php

namespace App\Data\Scrape;

use Spatie\LaravelData\Data;

class ScrapeDTO extends Data
{
    public function __construct(
        public Data $content,
    ) {
    }
}
