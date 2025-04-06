<?php

namespace App\Models;

use App\Enums\ProfileStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Searchable;

class Profile extends Model
{
    /** @use HasFactory<\Database\Factories\ProfileFactory> */
    use HasFactory;

    use Searchable;

    protected $fillable = [
        'username',
        'name',
        'bio',
        'likes',
        'status',
        'last_scraped_at',
    ];

    protected $casts = [
        'likes' => 'integer',
        'status' => ProfileStatus::class,
        'last_scraped_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'username';
    }

    public function getWasScrapedAttribute(): bool
    {
        if (! $this->last_scraped_at) {
            return false;
        }

        if ($this->likes >= config('scraping.likes_threshold')) {
            return $this->last_scraped_at->diffInHours(now()) <= config('scraping.scrape_interval.above_100k');
        }

        return $this->last_scraped_at->diffInHours(now()) <= config('scraping.scrape_interval.up_to_100k');
    }

    #[SearchUsingFullText(['username', 'name', 'bio'])]
    public function toSearchableArray(): array
    {
        return [
            'username' => $this->username,
            'name' => $this->name,
            'bio' => $this->bio,
        ];
    }
}
