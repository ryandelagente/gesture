<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSqlFile extends Command
{
    protected $signature = 'db:import-file {path : Absolute path to the .sql file}';

    protected $description = 'Import a raw .sql dump using the application DB connection (foreign key checks disabled). Use for migrating data when the mysql CLI auth differs from the app.';

    public function handle(): int
    {
        $path = $this->argument('path');

        if (!is_file($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $sql = file_get_contents($path);
        $this->info('Importing ' . number_format(strlen($sql)) . " bytes from {$path} ...");

        try {
            $pdo = DB::connection()->getPdo();
            $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
            $pdo->exec('SET SQL_MODE=""');
            $pdo->exec($sql);
            $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Throwable $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Import complete. Row counts:');
        foreach (['users', 'workspaces', 'plans', 'projects'] as $table) {
            try {
                $this->line("  {$table} = " . DB::table($table)->count());
            } catch (\Throwable $e) {
                $this->line("  {$table} = (error: {$e->getMessage()})");
            }
        }

        return self::SUCCESS;
    }
}
