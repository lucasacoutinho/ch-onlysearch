<?php

namespace App\Exceptions\Scrape;

use Exception;

class ExpectedFieldNotFoundException extends Exception
{
    public function __construct(string $field)
    {
        parent::__construct("Expected field {$field} not found");
    }
}
