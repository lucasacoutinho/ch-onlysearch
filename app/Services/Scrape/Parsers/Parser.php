<?php

namespace App\Services\Scrape\Parsers;

interface Parser
{
    public function parse(mixed $content): array;
}
