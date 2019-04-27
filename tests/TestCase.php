<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptorTests;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{

    protected static function getConnection(): \PDO
    {
        $pdo = new \PDO(
            sprintf(getenv('DB_DSN'), self::getDbName()),
            getenv('DB_USER'),
            getenv('DB_PASSWORD')
        );
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    protected static function getDbName(): string
    {
        return getenv('DB_NAME');
    }

}