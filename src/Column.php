<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor;


abstract class Column
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name, array $options = [])
    {
        $this->name = $name;
        $this->setOptions($options);
    }

    abstract protected function setOptions(array $options);

}