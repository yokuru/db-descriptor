<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor;


abstract class Table
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Column[]
     */
    private $columns = [];

    public function __construct(string $name, array $columns, array $options = [])
    {
        $this->name = $name;
        $this->columns = $columns;
        $this->setOptions($options);
    }

    abstract protected function setOptions(array $options);

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