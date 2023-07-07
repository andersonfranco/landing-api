<?php

namespace App\Framework\Models\Traits;

use RedBeanPHP as DB;

trait ExportToCSV
{
    public function exportToCSV(string $filename = null): void
    {
        $sql = 'SELECT * FROM `' . $this->table . '`';

        if ($filename !== null) {
            DB\R::csv($sql, [], null, $filename . '.csv', false);
            return;
        }

        DB\R::csv($sql, [], null);
        exit;
    }
}