<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor;

abstract class Reference
{
    /**
     * Referenced table name
     * @var string
     */
    protected $referencedTable;

    /**
     * Referenced column name
     * @var string
     */
    protected $referencedColumn;

    /**
     * ForeignKey constructor.
     * @param string $referencedTable
     * @param string $referencedColumn
     */
    public function __construct(string $referencedTable, string $referencedColumn)
    {
        $this->referencedTable = $referencedTable;
        $this->referencedColumn = $referencedColumn;
    }

    public function getReferencedTable(): string
    {
        return $this->referencedTable;
    }

    public function getReferencedColumn(): string
    {
        return $this->referencedColumn;
    }
}