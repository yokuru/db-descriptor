<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor\MySql;

use Yokuru\DbDescriptor\Constraint;
use Yokuru\DbDescriptor\Reference;
use Yokuru\DbDescriptor\Table;

/**
 * @see https://dev.mysql.com/doc/refman/8.0/en/tables-table.html
 */
class MySqlTable extends Table
{

    /**
     * @return Column[]
     */
    public function getPrimaryKeys(): array
    {
        foreach ($this->constraints as $c) {
            if ($c->getType() === Constraint::TYPE_PRIMARY_KEY) {
                $columns = [];
                foreach ($c->getColumns() as $column) {
                    $columns[] = $this->getColumn($column);
                }
                return $columns;
            }
        }
        return [];
    }

    /**
     * @return Reference[]
     */
    public function getForeignKeys(): array
    {
        $foreignKeys = [];
        foreach ($this->columns as $name => $c) {
            if ($c->hasReference()) {
                $foreignKeys[$name] = $c->getReference();
            }
        }
        return $foreignKeys;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->options['TABLE_COMMENT'];
    }

    /**
     * @param Constraint[] $constraints
     */
    public function setConstraints(array $constraints)
    {
        $this->constraints = $constraints;
    }
}