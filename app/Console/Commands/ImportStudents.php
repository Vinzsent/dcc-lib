<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:students {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import students from an Excel file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $this->info("Importing students from {$file}...");

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\StudentsImport, $file);
            $this->info("Import completed successfully!");
        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
