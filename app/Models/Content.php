<?php

namespace App\Models;

use App\Enums\AgeRating;
use App\Enums\ContentType;
use Database\Factories\ContentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'type', 'title', 'slug', 'synopsis', 'release_year', 'release_date', 'tagline', 'duration_min',
    'age_rating', 'cast_members', 'poster', 'backdrop', 'trailer_url', 'video_path', 'is_featured',
])]
class Content extends Model
{
    /** @use HasFactory<ContentFactory> */
    use HasFactory;

    public const int MAX_FEATURED = 5;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ContentType::class,
            'age_rating' => AgeRating::class,
            'release_date' => 'date',
            'cast_members' => 'array',
            'is_featured' => 'boolean',
            'views' => 'integer',
        ];
    }

    /**
     * @return BelongsToMany<Genre, $this>
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    /**
     * @return HasMany<Season, $this>
     */
    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class)->orderBy('season_number');
    }

    /**
     * @return HasMany<Rating, $this>
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Limit the catalog to what the given profile may see (kids profiles get SU and 7+ only).
     */
    #[Scope]
    protected function visibleTo(Builder $query, Profile $profile): void
    {
        $query->when($profile->is_kids, fn (Builder $query) => $query->whereIn('age_rating', AgeRating::forKids()));
    }

    #[Scope]
    protected function featured(Builder $query): void
    {
        $query->where('is_featured', true)->latest('release_date')->limit(self::MAX_FEATURED);
    }

    /**
     * Case-insensitive title search with LIKE wildcards in the term escaped.
     */
    #[Scope]
    protected function titleMatches(Builder $query, string $term): void
    {
        $escapedTerm = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], mb_strtolower($term));

        $query->whereRaw("LOWER(title) LIKE ? ESCAPE '!'", ['%'.$escapedTerm.'%']);
    }

    public function isMovie(): bool
    {
        return $this->type === ContentType::Movie;
    }

    public function isSeries(): bool
    {
        return $this->type === ContentType::Series;
    }

    public function isVisibleTo(Profile $profile): bool
    {
        return ! $profile->is_kids || $this->age_rating->isSuitableForKids();
    }

    public function posterUrl(): ?string
    {
        return $this->poster ? Storage::disk('public')->url($this->poster) : null;
    }

    public function backdropUrl(): ?string
    {
        return $this->backdrop ? Storage::disk('public')->url($this->backdrop) : $this->posterUrl();
    }

    /**
     * Privacy-friendly embed URL for a YouTube trailer, or null when there is no valid trailer.
     */
    public function trailerEmbedUrl(): ?string
    {
        if (! $this->trailer_url) {
            return null;
        }

        if (! preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/)|youtu\.be/)([\w-]{11})~', $this->trailer_url, $matches)) {
            return null;
        }

        return 'https://www.youtube-nocookie.com/embed/'.$matches[1];
    }

    public function formattedDuration(): ?string
    {
        if (! $this->duration_min) {
            return null;
        }

        $hours = intdiv($this->duration_min, 60);
        $minutes = $this->duration_min % 60;

        return trim(($hours ? "{$hours}j " : '').($minutes ? "{$minutes}m" : ''));
    }

    /**
     * The fields the Alpine hero slider, detail modal and live search need.
     *
     * @return array{id: int, type: string, title: string, slug: string, synopsis: string, year: int, date: ?string, tagline: ?string, duration: ?string, age_rating: string, genres: list<string>, cast: list<string>, poster: ?string, backdrop: ?string, trailer: ?string, url: string, watch_url: string}
     */
    public function toCardData(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->label(),
            'title' => $this->title,
            'slug' => $this->slug,
            'synopsis' => $this->synopsis,
            'year' => $this->release_year,
            'date' => $this->release_date?->translatedFormat('j F'),
            'tagline' => $this->tagline,
            'duration' => $this->formattedDuration(),
            'age_rating' => $this->age_rating->value,
            'genres' => $this->genres->pluck('name')->values()->all(),
            'cast' => $this->cast_members ?? [],
            'poster' => $this->posterUrl(),
            'backdrop' => $this->backdropUrl(),
            'trailer' => $this->trailerEmbedUrl(),
            'url' => route('contents.show', $this),
            'watch_url' => route('watch', $this),
        ];
    }
}
