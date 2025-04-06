<?php

namespace App\Data\Scrape;

use Spatie\LaravelData\Data;

class ProfileDTO extends Data
{
    public function __construct(
        public string $username,
        public string $name,
        public string $bio,
        public int $likes,
    ) {
    }
}
