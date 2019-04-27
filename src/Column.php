<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor;


abstract class Column
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var bool
     */
    protected $autoIncrement = false;

    /**
     * @var bool
     */
    protected $unsigned = false;

    /**
     * @var bool
     */
    protected $notNull = false;

    /**
     * For enum columns, values of enum
     *
     * @var string[]
     */
    protected $enumValues = [];

    /**
     * @param string $name
     * @param array $options
     */
    public function __construct(string $name, array $options)
    {
        $this->name = $name;
        $this->options = $options;
    }

    /**
     * @return bool
     */
    function isAutoIncrement(): bool
    {
        return $this->autoIncrement;
    }

    /**
     * @return bool
     */
    function isUnsigned(): bool
    {
        return $this->unsigned;
    }

    /**
     * @return bool
     */
    function isNotNull(): bool
    {
        return $this->notNull;
    }

    /**
     *
     *
     * @return array
     */
    function getEnumValues(): array
    {
        return $this->enumValues;
    }
}