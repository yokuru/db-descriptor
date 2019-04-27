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

    public function __construct(string $name, array $options = [])
    {
        $this->name = $name;
        $this->options = $options;
    }
}