<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor\MySql;


use Yokuru\DbDescriptor\Column;

class MySqlColumn extends Column
{
    /**
     * @return string
     */
    public function tableCatalog(): string
    {
        return $this->options['TABLE_CATALOG'];
    }

    /**
     * @return string
     */
    public function tableSchema(): string
    {
        return $this->options['TABLE_SCHEMA'];
    }

    /**
     * @return string
     */
    public function tableName(): string
    {
        return $this->options['TABLE_NAME'];
    }

    /**
     * @return string
     */
    public function columnName(): string
    {
        return $this->options['COLUMN_NAME'];
    }

    /**
     * @return int
     */
    public function ordinalPosition(): int
    {
        return $this->options['ORDINAL_POSITION'];
    }

    /**
     * @return string|null
     */
    public function columnDefault(): ?string
    {
        return $this->options['COLUMN_DEFAULT'];
    }

    /**
     * @return string
     */
    public function isNullable(): string
    {
        return $this->options["IS_NULLABLE"];
    }

    /**
     * @return string
     */
    public function dataType(): string
    {
        return $this->options['DATA_TYPE'];
    }

    /**
     * @return int|null
     */
    public function characterMaximumLength(): ?int
    {
        return $this->options['CHARACTER_MAXIMUM_LENGTH'];
    }

    /**
     * @return int|null
     */
    public function characterOctetLength(): ?int
    {
        return $this->options['CHARACTER_OCTET_LENGTH'];
    }

    /**
     * @return int|null
     */
    public function numericPrecision(): ?int
    {
        return $this->options['NUMERIC_PRECISION'];
    }

    /**
     * @return int|null
     */
    public function numericScale(): ?int
    {
        return $this->options['NUMERIC_SCALE'];
    }

    /**
     * @return int|null
     */
    public function datetimePrecision(): ?int
    {
        return $this->options['DATETIME_PRECISION'];
    }

    /**
     * @return string|null
     */
    public function characterSetName(): ?string
    {
        return $this->options['CHARACTER_SET_NAME'];
    }

    /**
     * @return string|null
     */
    public function collationName(): ?string
    {
        return $this->options['COLLATION_NAME'];
    }

    /**
     * @return string
     */
    public function columnType(): string
    {
        return $this->options['COLUMN_TYPE'];
    }

    /**
     * @return string
     */
    public function columnKey(): string
    {
        return $this->options['COLUMN_KEY'];
    }

    /**
     * @return string
     */
    public function extra(): string
    {
        return $this->options['EXTRA'];
    }

    /**
     * @return string
     */
    public function privileges(): string
    {
        return $this->options['PRIVILEGES'];
    }

    /**
     * @return string
     */
    public function columnComment(): string
    {
        return $this->options['COLUMN_COMMENT'];
    }

    /**
     * @return string
     */
    public function generationExpression(): string
    {
        return $this->options['GENERATION_EXPRESSION'];
    }
}