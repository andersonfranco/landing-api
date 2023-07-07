<?php 
declare(strict_types=1);

if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 70400) { die('PHP version >= 7.4 required'); }
require __DIR__ . '/vendor/autoload.php';

use App\Framework\Database\Connection;
use App\Framework\Utils\Str;
use App\Framework\Models\Load;

class CLI {

    protected $models = [];

    public function __construct(protected Connection $db) {}

    public function help(): void
    {
        echo <<<HELP
Usage: php cli.php [OPTIONS]

Options:
    -h, --help                      Show this help message
    -l, --list-tables               List all tables
    -d, --describe-table <table>    Describe table
    --list-models                   List all models
    --delete-table <table>          Delete table
    --empty-table <table>           Empty table
    --delete-all-tables=confirm     Delete all tables
    --export-to-xlsx <table>        Export to XLSX
    --export-to-csv <table>         Export to CSV
HELP;
        echo PHP_EOL, PHP_EOL;
        exit;
    }
    
    public function listTables(): void
    {
        $count = 0;
        $tables = $this->db->getAllTables();
    
        if (!count($tables))
            die("The database is empty!" . PHP_EOL . PHP_EOL);
    
        echo "Tables:\n";
        foreach ($tables as $name) {
            echo "#" . (++$count) . " {$name}" . PHP_EOL;
        }
        
        echo PHP_EOL;
    }

    public function listModels(): void
    {
        $count = 0;
    
        if (!count($this->models))
            die("No models found!" . PHP_EOL . PHP_EOL);
    
        echo "Models:\n";
        foreach ($this->models as $model) {
            echo "#" . (++$count) . " {$model->table} (" . implode(',', $model->fields) . ")" . PHP_EOL;
        }
        
        echo PHP_EOL;
    }
    
    public function describeTable(string|array $tablesToDescribe): void
    {    
        $tables = $this->db->getAllTables();
    
        if (!is_array($tablesToDescribe)) {
            $tablesToDescribe = [$tablesToDescribe];
        }
    
        foreach ($tablesToDescribe as $table) {
            $count = 0;
            $tableName = Str::filterTableName($table);
    
            if (!in_array($tableName, $tables)) {
                die("Table $tableName not found!" . PHP_EOL . PHP_EOL);
            }
    
            $columns = $this->db->getAllColumns($tableName);
            uksort($columns, function ($a, $b) {
                if ($a == 'id' || substr($b, 0, 1) === '_') {
                    return -1;
                } else {
                    return strnatcasecmp($a, $b);
                }
            });
            
            if (!count($columns)) {
                die("Table $tableName is empty!" . PHP_EOL . PHP_EOL);
            }
    
            echo "Columns of $tableName:" . PHP_EOL;
            foreach ($columns as $name => $type) {
                echo "#" . (++$count) . " $name ($type)\n";
            }
            echo PHP_EOL;
        }
    }
    
    public function deleteTable(string $tableName): void
    {
        $this->db->deleteTable($tableName);
        echo "Table $tableName deleted!" . PHP_EOL . PHP_EOL;
    }
    
    public function emptyTable(string $tableName): void
    {
        $this->db->emptyTable($tableName);
        echo "Table $tableName emptied!" . PHP_EOL . PHP_EOL;
    }
    
    public function deleteAllTables(): void
    {
        $this->db->deleteAllTables();
        echo "All tables deleted!" . PHP_EOL . PHP_EOL;
    }
    
    public function exportToXLSX(string $tableName): void
    {
        if (!array_key_exists($tableName, $this->models)) 
            die("Model not found!" . PHP_EOL . PHP_EOL);

        $this->models[$tableName]->exportToXLSX($this->models[$tableName]->plural);
    }

    public function exportToCSV(string $tableName): void
    {
        if (!array_key_exists($tableName, $this->models)) 
            die("Model not found!" . PHP_EOL . PHP_EOL);

        $this->models[$tableName]->exportToCSV($this->models[$tableName]->plural);
    }

    public function run(): void
    {
        $this->models = Load::allModels();

        $options = getopt('hld:', [
            'help', 'list-tables', 'describe-table:', 
            'delete-table:', 'empty-table:', 'delete-all-tables::',
            'export-to-xlsx:', 'export-to-csv:', 'list-models'
        ]);
    
        echo PHP_EOL;
    
        if (empty($options)) {
            $options = ['h' => ''];
        }
    
        if (isset($options['help']) || isset($options['h'])) {
            $this->help();
        }
    
        if (isset($options['list-models'])) {
            $this->listModels();
        }
    
        if (isset($options['list-tables']) || isset($options['l'])) {
            $this->listTables();
        }
    
        if (isset($options['describe-table'])) {
            $this->describeTable($options['describe-table']);
        }
    
        if (isset($options['d'])) {
            $this->describeTable($options['d']);
        }
    
        if (isset($options['delete-table'])) {
            $this->deleteTable($options['delete-table']);
        }
    
        if (isset($options['empty-table'])) {
            $this->emptyTable($options['empty-table']);
        }
    
        if (isset($options['delete-all-tables'])) { 
            if (!$options['delete-all-tables'] || strtolower($options['delete-all-tables']) !== 'confirm') {
                die('You must confirm with --delete-all-tables=confirm' . PHP_EOL . PHP_EOL);
            }
            $this->deleteAllTables();
        }

        if (isset($options['export-to-xlsx'])) {
            $this->exportToXLSX($options['export-to-xlsx']);
        }

        if (isset($options['export-to-csv'])) {
            $this->exportToCSV($options['export-to-csv']);
        }
    }
}

try {
    (new CLI(new Connection()))->run();
} catch (Exception $e) {
    echo $e->getMessage();
}