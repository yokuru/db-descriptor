<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor;

abstract class Table
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Column[]
     */
    protected $columns = [];

    /**
     * @var Index[]
     */
    protected $indexes = [];

    /**
     * Column name of primary keys
     * @var string[]
     */
    protected $primaryKeys = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param string $name
     * @param Column[] $columns
     * @param Index[] $indexes
     * @param array $options
     */
    public function __construct(string $name, array $columns, array $indexes, array $options = [])
    {
        $this->name = $name;
        $this->columns = $columns;
        $this->indexes = $indexes;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return Index[]
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * @return string[]
     */
    public function getPrimaryKeys(): array
    {
        return $this->primaryKeys;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string $name
     * @return Column
     */
    public function getColumn(string $name): Column
    {
        if (!array_key_exists($name, $this->columns)) {
            throw new \InvalidArgumentException("Column {$name} is not exists.");
        }

        return $this->columns[$name];
    }

    /**
     * @param array $primaryKeys
     */
    public function setPrimaryKeys(array $primaryKeys)
    {
        $this->primaryKeys = $primaryKeys;
    }
}