<?php

namespace App\Exceptions\Profile;

use Exception;
use Illuminate\Http\JsonResponse;

class ProfileNotFoundException extends Exception
{
    public function __construct(string $username)
    {
        parent::__construct("Profile {$username} not found");
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 404);
    }
}
