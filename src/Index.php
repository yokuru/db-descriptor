<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor;

abstract class Index
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $columns;

    /**
     * @param string $name
     * @param string[] $columns
     */
    public function __construct(string $name, array $columns)
    {
        $this->name = $name;
        $this->columns = $columns;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }
}