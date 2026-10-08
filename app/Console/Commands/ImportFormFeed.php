<?php

namespace App\Console\Commands;

use App\Actions\ImportFormFeed as ImportFormFeedAction;
use Illuminate\Console\Command;
use Throwable;

class ImportFormFeed extends Command
{
    protected $signature = 'forms:import
        {--path= : Relative path to the JSON feed, relative to the project root}';

    protected $description = 'Import a JSON form feed into the database';

    public function handle(ImportFormFeedAction $importer): int
    {
        $relativePath = $this->option('path') ?? config('forms.feed_path');
        $path = base_path($relativePath);

        try {
            $result = $importer->handle($path);
        } catch (Throwable $exception) {
            report($exception);

            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Imported form: {$result['form']->name}");

        $this->table(
            ['Entity', 'Imported'],
            [
                ['Sections', $result['sections']],
                ['Fields', $result['fields']],
                ['Options', $result['options']],
            ],
        );

        return self::SUCCESS;
    }
}