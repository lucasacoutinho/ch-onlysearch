<?php

namespace App\Http\Controllers;

use App\Exceptions\Profile\ProfileNotFound;
use App\Http\Requests\ProfileSearchRequest;
use App\Http\Resources\ProfileResource;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProfileController extends Controller
{
    public function __construct(
        protected ProfileService $profileService,
    ) {}

    public function index(ProfileSearchRequest $request): AnonymousResourceCollection
    {
        $profiles = $this->profileService->search($request->input('query'));
        return ProfileResource::collection($profiles);
    }

    public function show(string $username): ProfileResource
    {
        $profile = $this->profileService->findByUsername($username);

        if (!$profile) {
            throw new ProfileNotFound($username);
        }

        return ProfileResource::make($profile);
    }

    public function scrape(string $username): JsonResponse
    {
        $this->profileService->scrape($username);
        return response()->json(['message' => 'Scraping started']);
    }

    public function status(string $username): JsonResponse
    {
        $profile = $this->profileService->findByUsername($username);

        if (!$profile) {
            throw new ProfileNotFound($username);
        }

        return response()->json(['status' => $profile->status->value]);
    }
}
