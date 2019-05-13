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
     * @var ?Reference
     */
    protected $reference;

    /**
     * @var string[]
     */
    protected $enumValues = [];

    /**
     * @var array
     */
    protected $options = [];

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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * For foreign key
     * @return Reference
     */
    public function getReference(): Reference
    {
        return $this->reference;
    }

    /**
     * @return bool
     */
    public function hasReference(): bool
    {
        return $this->reference !== null;
    }

    /**
     * For enum columns, values of enum
     * @return array
     */
    public function getEnumValues(): array
    {
        return $this->enumValues;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
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
     * @return bool
     */
    abstract public function isAutoIncrement(): bool;

    /**
     * @return bool
     */
    abstract public function isUnsigned(): bool;

    /**
     * @return bool
     */
    abstract public function isNotNull(): bool;
}