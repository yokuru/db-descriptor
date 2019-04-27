<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor;


abstract class Database
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Table[]
     */
    private $tables = [];

    public function __construct(string $name, array $tables)
    {
        $this->name = $name;
        $this->tables = $tables;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Table[]
     */
    public function getTables(): array
    {
        return $this->tables;
    }

    /**
     * @param string $name
     * @return Table
     */
    public function getTable(string $name): Table
    {
        if (!array_key_exists($name, $this->tables)) {
            throw new \InvalidArgumentException("Table {$name} is not exists.");
        }

        return $this->tables[$name];
    }
}