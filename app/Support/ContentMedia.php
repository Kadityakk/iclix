<?php

namespace App\Support;

use App\Models\Content;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Stores uploaded artwork on the public disk and videos on the private local disk.
 */
class ContentMedia
{
    /**
     * Seeded demo files are shared between records, so they are never deleted.
     */
    private const string SHARED_PREFIX = 'demo/';

    public function storeImage(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public');
    }

    public function storeVideo(UploadedFile $file): string
    {
        return $file->store('videos', 'local');
    }

    public function deleteImage(?string $path): void
    {
        $this->delete('public', $path);
    }

    public function deleteVideo(?string $path): void
    {
        $this->delete('local', $path);
    }

    /**
     * Remove every file that belongs to the content, including its episodes.
     */
    public function deleteAllFor(Content $content): void
    {
        $this->deleteImage($content->poster);
        $this->deleteImage($content->backdrop);
        $this->deleteVideo($content->video_path);

        $content->loadMissing('seasons.episodes');

        foreach ($content->seasons as $season) {
            foreach ($season->episodes as $episode) {
                $this->deleteImage($episode->thumbnail);
                $this->deleteVideo($episode->video_path);
            }
        }
    }

    private function delete(string $disk, ?string $path): void
    {
        if ($path !== null && ! str_starts_with($path, self::SHARED_PREFIX)) {
            Storage::disk($disk)->delete($path);
        }
    }
}
