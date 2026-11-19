<?php

namespace App\Console\Commands;

use App\Models\Content;
use App\Support\Imdb\ImdbClient;
use App\Support\Imdb\ImdbSync;
use App\Support\Imdb\ImdbUnavailableException;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('imdb:sync {--id=* : Only sync these IMDb IDs}')]
#[Description('Refresh titles, synopses, posters, ratings and details from IMDb (via OMDb)')]
class SyncImdbData extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(ImdbClient $client, ImdbSync $sync): int
    {
        if (! $client->isConfigured()) {
            $this->components->error('Isi OMDB_API_KEY di .env dulu. Kunci gratis: https://www.omdbapi.com/apikey.aspx');

            return self::FAILURE;
        }

        $contents = Content::query()
            ->whereNotNull('imdb_id')
            ->when($this->option('id'), fn ($query, array $ids) => $query->whereIn('imdb_id', $ids))
            ->orderBy('title')
            ->get();

        $failures = 0;

        foreach ($contents as $content) {
            $this->components->task($content->title, function () use ($sync, $content, &$failures) {
                try {
                    $sync->sync($content);

                    return true;
                } catch (ImdbUnavailableException $exception) {
                    $failures++;
                    $this->components->warn($exception->getMessage());

                    return false;
                }
            });
        }

        $this->components->info(($contents->count() - $failures)." dari {$contents->count()} judul disinkronkan.");

        return $failures === 0 ? self::SUCCESS : self::FAILURE;
    }
}
