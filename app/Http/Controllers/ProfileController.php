<?php

namespace App\Http\Controllers;

use App\Exceptions\Profile\ProfileNotFound;
use App\Http\Requests\ProfileSearchRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group('Profiles', 'Profiles API')]
class ProfileController extends Controller
{
    public function __construct(
        protected ProfileService $profileService,
    ) {}

    /**
     * Search for profiles
     */
    #[ResponseFromApiResource(ProfileResource::class, Profile::class)]
    public function index(ProfileSearchRequest $request): AnonymousResourceCollection
    {
        $profiles = $this->profileService->search($request->input('query'));
        return ProfileResource::collection($profiles);
    }

    /**
     * Get a profile by username
     */
    #[UrlParam('username', 'The username of the profile')]
    #[ResponseFromApiResource(ProfileResource::class, Profile::class)]
    public function show(string $username): ProfileResource
    {
        $profile = $this->profileService->findByUsername($username);

        if (!$profile) {
            throw new ProfileNotFound($username);
        }

        return ProfileResource::make($profile);
    }

    /**
     * Scrape a profile
     */
    #[UrlParam('username', 'The username of the profile')]
    #[Response(['message' => 'Scraping started'])]
    public function scrape(string $username): JsonResponse
    {
        $this->profileService->scrape($username);
        return response()->json(['message' => 'Scraping started']);
    }

    /**
     * Get the scraping status of a profile
     */
    #[UrlParam('username', 'The username of the profile')]
    #[Response(['status' => 'pending'])]
    public function status(string $username): JsonResponse
    {
        $profile = $this->profileService->findByUsername($username);

        if (!$profile) {
            throw new ProfileNotFound($username);
        }

        return response()->json(['status' => $profile->status->value]);
    }
}
