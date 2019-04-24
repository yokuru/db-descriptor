<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptorTests;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{

    protected function getConnection(): \PDO
    {
        return new \PDO(
            sprintf(getenv('DB_DSN'),getenv('DB_NAME')),
            getenv('DB_USER'),
            getenv('DB_PASSWORD')
        );
    }

}