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
        $dbName = $this->conn->query('SELECT database() AS name')->fetchObject()->name;
        return new MySqlDatabase($dbName, $this->describeTables($dbName));
    }

    /**
     * @param string $dbName
     * @return Table[]
     */
    public function describeTables(string $dbName): array
    {
        $stmt = $this->conn->prepare('SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = :db');
        $stmt->execute([
            'db' => $dbName,
        ]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $tables = [];
        foreach ($rows as $row) {
            $tableName = $row['TABLE_NAME'];
            $tables[$tableName] = new MySqlTable($tableName, $this->describeColumns($dbName, $tableName), $row);
        }

        return $tables;
    }

    /**
     * @param string $dbName
     * @param string $tableName
     * @return Column[]
     */
    public function describeColumns(string $dbName, string $tableName): array
    {
        $stmt = $this->conn->prepare('SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table');
        $stmt->execute([
            'db' => $dbName,
            'table' => $tableName,
        ]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $columns = [];
        foreach ($rows as $row) {
            $columnName = $row['COLUMN_NAME'];
            $columns[$columnName] = new MySqlColumn($columnName, $row);
        }

        return $columns;
    }

}