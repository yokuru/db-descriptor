<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor\MySql;

use Yokuru\DbDescriptor\Column;
use Yokuru\DbDescriptor\Database;
use Yokuru\DbDescriptor\Descriptor;
use Yokuru\DbDescriptor\Table;

class MySqlDescriptor extends Descriptor
{

    public function describeDatabase(): Database
    {
        $name = $this->conn->query('SELECT database() AS name')->fetchObject()->name;
        return new MySqlDatabase($name);
    }

    public function describeTable(string $tableName): Table
    {
        // TODO: Implement describeTable() method.
        $this->conn->query('SELECT * FROM information.schema WHERE TABLE_SCHEMA = :db', [
            'db' => $tableName,
        ]);

    }

    public function describeColumn(string $tableName, string $columnName): Column
    {
        // TODO: Implement describeColumn() method.
    }

}