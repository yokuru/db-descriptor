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
    private $_tables;
    private $_constraints;
    private $_keyColumnUsage;
    private $_indexes;
    private $_columns;

    /**
     * @return Database
     */
    public function describeDatabase(): Database
    {
        $dbName = $this->conn->query('SELECT database() AS name')->fetchObject()->name;
        $this->prepareDescription($dbName);
        return new MySqlDatabase($dbName, $this->describeTables($dbName));
    }

    /**
     * @param string $dbName
     * @return Table[]
     */
    public function describeTables(string $dbName): array
    {
        $tables = [];
        foreach ($this->getTableMeta($dbName) as $row) {
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
        // TODO duplicated processing
        $indexes = $this->describeIndexes($dbName, $tableName);

        // TODO unreadable
        $keyColumnUsages = $this->getKeyColumnUsageMeta($dbName, $tableName);

        $constraints = [];
        foreach ($this->getConstraintMeta($dbName, $tableName) as $row) {
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

    /**
     * @param string $dbName
     * @param string $tableName
     * @return Index[]
     */
    public function describeIndexes(string $dbName, string $tableName): array
    {
        $indexColumns = [];
        foreach ($this->getIndexMeta($dbName, $tableName) as $row) {
            $indexColumns[$row['INDEX_NAME']][] = $row['COLUMN_NAME'];
        }

        $indexes = [];
        foreach ($this->getIndexMeta($dbName, $tableName) as $row) {
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
        $columns = [];
        foreach ($this->getColumnMeta($dbName, $tableName) as $row) {
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

    private function prepareDescription(string $dbName)
    {
        $this->_tables = $this->getTableMeta($dbName);
        $this->_constraints = $this->getConstraintMeta($dbName);
        $this->_keyColumnUsage = $this->getKeyColumnUsageMeta($dbName);
        $this->_indexes = $this->getIndexMeta($dbName);
        $this->_columns = $this->getColumnMeta($dbName);
    }

    private function getTableMeta(string $dbName): array
    {
        if ($this->_tables !== null) {
            return $this->_tables;
        }

        $stmt = $this->conn->prepare('SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = :db');
        $stmt->execute([ 'db' => $dbName]);
        $this->_tables = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $this->getTableMeta($dbName);
    }

    private function getConstraintMeta(string $dbName, string $tableName = null): array
    {
        if ($this->_constraints !== null) {
            return $tableName ? ($this->_constraints[$tableName] ?? []) : $this->_constraints;
        }

        $stmt = $this->conn->prepare('SELECT * FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = :db');
        $stmt->execute(['db' => $dbName]);

        $this->_constraints = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $this->_constraints[$row['TABLE_NAME']][] = $row;
        }
        return $this->getConstraintMeta($dbName, $tableName);
    }

    private function getKeyColumnUsageMeta(string $dbName, string $tableName = null): array
    {
        if ($this->_keyColumnUsage !== null) {
            return $tableName ? ($this->_keyColumnUsage[$tableName] ?? []) : $this->_keyColumnUsage;
        }

        $stmt = $this->conn->prepare('SELECT * FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = :db');
        $stmt->execute(['db' => $dbName]);

        $this->_keyColumnUsage = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $this->_keyColumnUsage[$row['TABLE_NAME']][$row['CONSTRAINT_NAME' ]] = $row;
        }
        return $this->getKeyColumnUsageMeta($dbName, $tableName);
    }

    private function getIndexMeta(string $dbName, string $tableName = null): array
    {
        if ($this->_indexes !== null) {
            return $tableName ? ($this->_indexes[$tableName] ?? []) : $this->_indexes;
        }

        $stmt = $this->conn->prepare('SELECT * FROM information_schema.STATISTICS
          WHERE TABLE_SCHEMA = :db ORDER BY INDEX_NAME, SEQ_IN_INDEX');
        $stmt->execute(['db' => $dbName]);

        $this->_indexes = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $this->_indexes[$row['TABLE_NAME']][] = $row;
        }
        return $this->getIndexMeta($dbName, $tableName);
    }

    private function getColumnMeta(string $dbName, string $tableName = null): array
    {
        if ($this->_columns !== null) {
            return $tableName ? ($this->_columns[$tableName] ?? []) : $this->_columns;
        }

        $stmt = $this->conn->prepare('SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :db');
        $stmt->execute(['db' => $dbName]);

        $this->_columns = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $this->_columns[$row['TABLE_NAME']][] = $row;
        }
        return $this->getColumnMeta($dbName, $tableName);
    }
}