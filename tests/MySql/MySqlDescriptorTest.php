<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor\MySql;

use Yokuru\DbDescriptor\Constraint;
use Yokuru\DbDescriptorTests\TestCase;

class MySqlDescriptorTest extends TestCase
{
    /**
     * @var MySqlDescriptor
     */
    private $target;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // create tables
        $pdo = self::getConnection();
        $pdo->exec('CREATE TABLE table1(id int primary key, name varchar(100), created_at datetime)');
        $pdo->exec('CREATE INDEX index1 on table1 (name, id);');
        $pdo->exec('CREATE UNIQUE INDEX uq1 on table1 (name)');
        $pdo->exec('CREATE TABLE table2(id int, table1_id int)');
        $pdo->exec('ALTER TABLE table2 ADD CONSTRAINT fk1 FOREIGN KEY fk1 (table1_id) REFERENCES table1 (id)');
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        // drop tables
        $pdo = self::getConnection();
        $pdo->exec('DROP TABLE table2;');
        $pdo->exec('DROP TABLE table1;');
    }

    protected function setUp()
    {
        parent::setUp();
        $this->target = new MySqlDescriptor(self::getConnection());
    }

    public function testDescribeDatabase()
    {
        $db = $this->target->describeDatabase();
        $this->assertEquals(self::getDbName(), $db->getName());
        $this->assertEquals(2, count($db->getTables()));
    }

    public function testDescribeTables()
    {
        $tables = $this->target->describeTables(self::getDbName());
        $this->assertEquals(2, count($tables));
        $this->assertTrue(isset($tables['table1']));
        $this->assertTrue(isset($tables['table2']));
        $this->assertEquals(3, count($tables['table1']->getColumns()));
        $this->assertEquals(2, count($tables['table2']->getColumns()));

        $this->assertEquals(1, count($tables['table1']->getPrimaryKeys()));
        $this->assertEquals('id', $tables['table1']->getPrimaryKeys()[0]->getName());
    }

    public function testDescribeConstraints()
    {
        $constraints = $this->target->describeConstraints(self::getDbName(), 'table1');
        $this->assertEquals(2, count($constraints));
        $this->assertEquals(Constraint::TYPE_PRIMARY_KEY, $constraints['PRIMARY']->getType());
        $this->assertEquals(Constraint::TYPE_UNIQUE, $constraints['uq1']->getType());

        $columns = $constraints['PRIMARY']->getColumns();
        $this->assertEquals(1, count($columns));
        $this->assertEquals('id', $columns[0]);

        $columns = $constraints['uq1']->getColumns();
        $this->assertEquals(1, count($columns));
        $this->assertEquals('name', $columns[0]);

        $constraints = $this->target->describeConstraints(self::getDbName(), 'table2');
        $this->assertEquals(1, count($constraints));
        $this->assertEquals(Constraint::TYPE_FOREIGN_KEY, $constraints['fk1']->getType());

    }

    public function testDescribeIndexes()
    {
        $indexes = $this->target->describeIndexes(self::getDbName(), 'table1');
        $this->assertEquals(3, count($indexes));

        $index = $indexes['PRIMARY'];
        $this->assertEquals('PRIMARY', $index->getName());
        $this->assertEquals(1, count($index->getColumns()));
        $this->assertEquals('id', $index->getColumns()[0]);

        $index = $indexes['index1'];
        $this->assertEquals('index1', $index->getName());
        $this->assertEquals(2, count($index->getColumns()));
        $this->assertEquals('name', $index->getColumns()[0]);
        $this->assertEquals('id', $index->getColumns()[1]);

        $index = $indexes['uq1'];
        $this->assertEquals('uq1', $index->getName());
        $this->assertEquals(1, count($index->getColumns()));
        $this->assertEquals('name', $index->getColumns()[0]);
    }

    public function testDescribeColumns()
    {
        $columns = $this->target->describeColumns(self::getDbName(), 'table1');
        $this->assertEquals(3, count($columns));
        $this->assertTrue(isset($columns['id']));
        $this->assertTrue(isset($columns['name']));
        $this->assertTrue(isset($columns['created_at']));

        $columns = $this->target->describeColumns(self::getDbName(), 'table2');
        $this->assertEquals(2, count($columns));
        $this->assertTrue(isset($columns['id']));
        $this->assertTrue(isset($columns['table1_id']));

        $ref = $columns['table1_id']->getReference();
        $this->assertEquals('table1', $ref->getReferencedTable());
        $this->assertEquals('id', $ref->getReferencedColumn());
    }
}