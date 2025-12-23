<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BooksImport;

class ImportBooks extends Command
{
    protected $signature = 'books:import {file : Path to Excel file}';
    protected $description = 'Import books from Excel file';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $this->info('Starting import...');
        $this->info('File: ' . $file);

        $import = new BooksImport();

        Excel::import($import, $file);

        $this->info('Import completed!');
        $this->info('Imported: ' . $import->getImportedCount() . ' books');
        $this->info('Skipped: ' . $import->getSkippedCount() . ' rows');

        if (count($import->getErrors()) > 0) {
            $this->warn('Errors encountered: ' . count($import->getErrors()));
            foreach (array_slice($import->getErrors(), 0, 5) as $error) {
                $this->error($error['error']);
            }
        }

        return 0;
    }
}
