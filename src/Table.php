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
     * @var array
     */
    protected $options = [];

    /**
     * @param string $name
     * @param array $columns
     * @param array $options
     */
    public function __construct(string $name, array $columns, array $options = [])
    {
        $this->name = $name;
        $this->columns = $columns;
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
}