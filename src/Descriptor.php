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
     * @return Table[]
     */
    abstract function describeTables(string $tableName): array;

    /**
     * @param string $tableName
     * @param string $columnName
     * @return Column[]
     */
    abstract function describeColumns(string $tableName, string $columnName): array;

}