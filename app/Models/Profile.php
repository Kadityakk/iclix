<?php

namespace App\Models;

use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'avatar', 'is_kids'])]
class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use HasFactory;

    /**
     * Avatar presets mapped to their Tailwind gradient classes.
     *
     * @var array<string, string>
     */
    public const array AVATARS = [
        'violet' => 'from-violet-500 to-fuchsia-600',
        'rose' => 'from-rose-500 to-orange-400',
        'sky' => 'from-sky-500 to-indigo-600',
        'emerald' => 'from-emerald-400 to-teal-600',
        'amber' => 'from-amber-400 to-rose-500',
        'slate' => 'from-slate-400 to-slate-700',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_kids' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<WatchHistory, $this>
     */
    public function watchHistories(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }

    /**
     * @return BelongsToMany<Content, $this>
     */
    public function watchlistContents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'watchlists')->withTimestamps();
    }

    /**
     * @return HasMany<Rating, $this>
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function avatarGradient(): string
    {
        return self::AVATARS[$this->avatar] ?? self::AVATARS['violet'];
    }
}
