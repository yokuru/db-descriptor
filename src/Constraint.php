<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor;

abstract class Constraint
{
    const TYPE_PRIMARY_KEY = 'pk';
    const TYPE_FOREIGN_KEY = 'fk';
    const TYPE_UNIQUE = 'uq';

    /**
     * Constraint name
     * @var string
     */
    private $name;

    /**
     * Column names
     * @var string[]
     */
    private $columns = [];

    /**
     * Constraint type
     * @var string
     */
    private $type = [];

    public function __construct(string $name, array $columns, string $type)
    {
        $this->name = $name;
        $this->columns = $columns;
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getType(): string
    {
        return $this->type;
    }
}