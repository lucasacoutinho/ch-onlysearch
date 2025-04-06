<?php

namespace App\Services\Scrape\Pages;

use App\Data\Scrape\ProfileDTO;
use App\Data\Scrape\ScrapeDTO;
use App\Services\Scrape\Parsers\Parser;
use App\Services\Scrape\Parsers\ProfileParser;

class Profile implements Page
{
    public function __construct(
        protected string $username,
    ) {}

    public function getUrl(): string
    {
        return "https://www.onlyfans.com/{$this->username}";
    }

    public function getParser(): Parser
    {
        return new ProfileParser();
    }

    public function getHandler(): callable
    {
        return function (string $html) {
            $parser  = new ProfileParser();
            $content = $parser->parse($html);

            $result = new ScrapeDTO(content: ProfileDTO::from($content));

            return $result;
        };
    }
}
