<?php

namespace App\Models;

use Database\Factories\WatchHistoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['content_id', 'episode_id', 'position_sec', 'duration_sec'])]
class WatchHistory extends Model
{
    /** @use HasFactory<WatchHistoryFactory> */
    use HasFactory;

    /**
     * Share of the video after which it counts as finished.
     */
    private const float FINISHED_RATIO = 0.95;

    /**
     * @return BelongsTo<Profile, $this>
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * @return BelongsTo<Content, $this>
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }

    /**
     * @return BelongsTo<Episode, $this>
     */
    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

    public function progressPercent(): int
    {
        if ($this->duration_sec <= 0) {
            return 0;
        }

        return (int) min(100, round($this->position_sec / $this->duration_sec * 100));
    }

    public function isFinished(): bool
    {
        return $this->duration_sec > 0 && $this->position_sec >= $this->duration_sec * self::FINISHED_RATIO;
    }
}
