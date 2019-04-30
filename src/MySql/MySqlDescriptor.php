<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor\MySql;

use Yokuru\DbDescriptor\Column;
use Yokuru\DbDescriptor\Constraint;
use Yokuru\DbDescriptor\Database;
use Yokuru\DbDescriptor\Descriptor;
use Yokuru\DbDescriptor\Index;
use Yokuru\DbDescriptor\Table;

class MySqlDescriptor extends Descriptor
{
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

            // set constraints
            $table->setConstraints($this->describeConstraints($dbName, $tableName));

            $tables[$tableName] = $table;
        }

        return $tables;
    }

    /**
     * @param string $dbName
     * @param string $tableName
     * @return Constraint[]
     */
    public function describeConstraints(string $dbName, string $tableName): array
    {
        $stmt = $this->conn->prepare('
          SELECT
            *
          FROM
            information_schema.TABLE_CONSTRAINTS
          WHERE
            TABLE_SCHEMA = :db
            AND TABLE_NAME = :table
        ');
        $stmt->execute([
            'db' => $dbName,
            'table' => $tableName,
        ]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // TODO duplicated processing
        $indexes = $this->describeIndexes($dbName, $tableName);

        // TODO unreadable
        $keyColumnUsages = $this->describeKeyColumnUsages($dbName, $tableName);

        $constraints = [];
        foreach ($rows as $row) {
            $name = $row['CONSTRAINT_NAME'];

            switch ($row['CONSTRAINT_TYPE']) {
                case 'PRIMARY KEY':
                    $constraints[$name] = new MySqlConstraint($name, $indexes[$name]->getColumns(), Constraint::TYPE_PRIMARY_KEY);
                    break;

                case 'FOREIGN KEY':
                    $foreignKey = new MySqlConstraint($name, [$keyColumnUsages[$name]['COLUMN_NAME']], Constraint::TYPE_FOREIGN_KEY);
                    $foreignKey->setReference(new MySqlReference($keyColumnUsages[$name]['REFERENCED_TABLE_NAME'], $keyColumnUsages[$name]['REFERENCED_COLUMN_NAME']));
                    $constraints[$name] = $foreignKey;
                    break;

                case 'UNIQUE':
                    $constraints[$name] = new MySqlConstraint($name, $indexes[$name]->getColumns(), Constraint::TYPE_UNIQUE);
                    break;

                default:
                    throw new \InvalidArgumentException("Unknown constraint type '{$row['CONSTRAINT_TYPE']}''");
            }
        }

        return $constraints;
    }

    private function describeKeyColumnUsages($dbName, $tableName): array
    {
        $stmt = $this->conn->prepare('
          SELECT
            *
          FROM
            information_schema.KEY_COLUMN_USAGE
          WHERE
            TABLE_SCHEMA = :db
            AND TABLE_NAME = :table
        ');

        $stmt->execute([
            'db' => $dbName,
            'table' => $tableName,
        ]);

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $keyColumnUsages = [];
        foreach ($rows as $row) {
            $keyColumnUsages[$row['CONSTRAINT_NAME' ]] = $row;
        }

        return $keyColumnUsages;
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

        // TODO duplicated processing
        foreach ($this->describeConstraints($dbName, $tableName) as $c) {
            if ($c->getType() === Constraint::TYPE_FOREIGN_KEY) {
                $columns[$c->getColumns()[0]]->setReference($c->getReference());
            }
        }

        return $columns;
    }
}