<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor\MySql;

use Yokuru\DbDescriptor\Column;
use Yokuru\DbDescriptor\Database;
use Yokuru\DbDescriptor\Descriptor;
use Yokuru\DbDescriptor\Index;
use Yokuru\DbDescriptor\Table;

class MySqlDescriptor extends Descriptor
{
    const INDEX_NAME_PK = 'PRIMARY';

    /**
     * @return Database
     */
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
        $stmt = $this->conn->prepare('
          SELECT
            *
          FROM
            information_schema.TABLES
          WHERE
            TABLE_SCHEMA = :db
        ');
        $stmt->execute([
            'db' => $dbName,
        ]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $tables = [];
        foreach ($rows as $row) {
            $tableName = $row['TABLE_NAME'];
            $indexes = $this->describeIndexes($dbName, $tableName);
            $table = new MySqlTable(
                $tableName,
                $this->describeColumns($dbName, $tableName),
                $indexes,
                $row
            );

            // set primary keys
            if (isset($indexes[self::INDEX_NAME_PK])) {
                $table->setPrimaryKeys($indexes[self::INDEX_NAME_PK]->getColumns());
            }

            $tables[$tableName] = $table;
        }

        return $tables;
    }

    /**
     * @param string $dbName
     * @param string $tableName
     * @return Index[]
     */
    public function describeIndexes(string $dbName, string $tableName): array
    {
        $stmt = $this->conn->prepare('
          SELECT
            *
          FROM
            information_schema.STATISTICS
          WHERE
            TABLE_SCHEMA = :db
            AND TABLE_NAME = :table
          ORDER BY
            INDEX_NAME,
            SEQ_IN_INDEX
        ');
        $stmt->execute([
            'db' => $dbName,
            'table' => $tableName,
        ]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $indexColumns = [];
        foreach ($rows as $row) {
            $indexColumns[$row['INDEX_NAME']][] = $row['COLUMN_NAME'];
        }

        $indexes = [];
        foreach ($rows as $row) {
            $indexName = $row['INDEX_NAME'];
            if (!isset($indexes[$indexName])) {
                $indexes[$indexName] = new MySqlIndex($indexName, $indexColumns[$indexName]);
            }
        }

        return $indexes;
    }

    /**
     * @param string $dbName
     * @param string $tableName
     * @return Column[]
     */
    public function describeColumns(string $dbName, string $tableName): array
    {
        $stmt = $this->conn->prepare('
          SELECT 
            *
          FROM
            information_schema.COLUMNS
          WHERE
            TABLE_SCHEMA = :db
            AND TABLE_NAME = :table
        ');
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