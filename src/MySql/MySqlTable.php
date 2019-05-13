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

    /**
     * @return string|null
     */
    public function engine(): ?string
    {
        return $this->options['ENGINE'];
    }

    /**
     * @return int|null
     */
    public function version(): ?int
    {
        return $this->options['VERSION'];
    }

    /**
     * @return string|null
     */
    public function rowFormat(): ?string
    {
        return $this->options['ROW_FORMAT'];
    }

    /**
     * @return int|null
     */
    public function tableRows(): ?int
    {
        return $this->options['TABLE_ROWS'];
    }

    /**
     * @return int|null
     */
    public function avgRowLength(): ?int
    {
        return $this->options['AVG_ROW_LENGTH'];
    }

    /**
     * @return int|null
     */
    public function dataLength(): ?int
    {
        return $this->options['DATA_LENGTH'];
    }

    /**
     * @return int|null
     */
    public function maxDataLength(): ?int
    {
        return $this->options['MAX_DATA_LENGTH'];
    }

    /**
     * @return int|null
     */
    public function indexLength(): ?int
    {
        return $this->options['INDEX_LENGTH'];
    }

    /**
     * @return int|null
     */
    public function dataFree(): ?int
    {
        return $this->options['DATA_FREE'];
    }

    /**
     * @return int|null
     */
    public function autoIncrement(): ?int
    {
        return $this->options['AUTO_INCREMENT'];
    }

    /**
     * @return string|null
     */
    public function createTime(): ?string
    {
        return $this->options['CREATE_TIME'];
    }

    /**
     * @return string|null
     */
    public function updateTime(): ?string
    {
        return $this->options['UPDATE_TIME'];
    }

    /**
     * @return string|null
     */
    public function checkTime(): ?string
    {
        return $this->options['CHECK_TIME'];
    }

    /**
     * @return string|null
     */
    public function tableCollation(): ?string
    {
        return $this->options['TABLE_COLLATION'];
    }

    /**
     * @return int|null
     */
    public function checksum(): ?int
    {
        return $this->options['CHECKSUM'];
    }

    /**
     * @return string|null
     */
    public function createOptions(): ?string
    {
        return $this->options['CREATE_OPTIONS'];
    }
}