<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor;

abstract class Descriptor
{

    /**
     * @var \PDO
     */
    protected $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @return Database
     */
    abstract function describeDatabase(): Database;

    /**
     * @param string $tableName
     * @return Table
     */
    abstract function describeTable(string $tableName): Table;

    /**
     * @param string $tableName
     * @param string $columnName
     * @return Column
     */
    abstract function describeColumn(string $tableName, string $columnName): Column;

}