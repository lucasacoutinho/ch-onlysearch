<?php

namespace App\Console\Commands;

use App\Models\Profile;
use App\Services\ProfileService;
use Illuminate\Console\Command;

class ScrapeProfiles extends Command
{
    protected $signature = 'scrape:profiles {--likes-min} {--likes-max}';

    protected $description = 'Scrape profiles from OnlyFans';

    public function handle(ProfileService $profileService): void
    {
        $this->info('Scraping profiles...');

        $min = $this->option('likes-min');
        $max = $this->option('likes-max');

        $profiles = $profileService->getProfilesBetweenLikes($min, $max);

        $profiles->each(function (Profile $profile) use ($profileService) {
            $this->info('Scraping profile ' . $profile->username);
            $profileService->scrape($profile->username);
        });

        $this->info('Scraped ' . $profiles->count() . ' profiles');
    }
}
