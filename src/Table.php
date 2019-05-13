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
     * @var Constraint[]
     */
    protected $constraints = [];

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
     * @return Constraint[]
     */
    public function getConstraints(): array
    {
        return $this->constraints;
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
     * @param string $key
     * @return mixed|null
     */
    public function getOption(string $key)
    {
        return $this->options[$key] ?? null;
    }

    /**
     * Get columns of primary key
     * @return Column[]
     */
    abstract public function getPrimaryKeys(): array;

    /**
     * Get foreign keys
     * @return Reference[]
     */
    abstract public function getForeignKeys(): array;

    /**
     * Get table comment
     * @return string
     */
    abstract public function getComment(): string;
}