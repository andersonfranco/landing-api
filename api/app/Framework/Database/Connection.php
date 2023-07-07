<?php

namespace App\Framework\Database;

use App\Config\Database;
use RedBeanPHP as DB;

class Connection
{
    public function __construct(
        protected bool $freeze = false,
        protected ?string $databaseName = null,
    ) {
        $this->openDB();
    }

    protected function getDatabaseName(): string
    {
        if ($this->databaseName !== null) {
            return $this->databaseName;
        }

        $rootDir = realpath(__DIR__ . '/../../Database' ) . '/';

        $filename = Database::$filename ?: 'db';

        $dbnPath = trim(dirname($filename), '/\\.');
        if ($dbnPath !== '') {
            if (!is_dir($rootDir . $dbnPath)) {
                mkdir($rootDir . $dbnPath, 0755, true);
            }
            $dbnPath .= '/';
        }
    
        $dbName = basename($filename, '.sqlite');

        return $rootDir . $dbnPath . $dbName . '.sqlite';
    }

    public function openDB(): void
    {
        if(DB\R::testConnection()) return;
        DB\R::setup('sqlite:' . $this->getDatabaseName());
        DB\R::freeze($this->freeze);
    }

    public function closeDB(): void
    {
        DB\R::close();
    }

    /** @return array<string> */
    public function getAllTables(): array
    {
        return DB\R::inspect();
    }

    /** @return array<string> */
    public function getAllColumns(string $table): array
    {
        return DB\R::getColumns($table);
    }

    public function deleteTable(string $table): void
    {
        DB\R::exec('DROP TABLE IF EXISTS `' . $table . '`');
    }

    public function emptyTable(string $table): void
    {
        DB\R::wipe($table);
    }

    public function deleteAllTables(): void
    {
        DB\R::nuke();
    }
}