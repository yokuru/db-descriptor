<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor\MySql;

use Yokuru\DbDescriptor\Constraint;
use Yokuru\DbDescriptorTests\TestCase;

class MySqlTableTest extends TestCase
{
    /**
     * @var MySqlTable
     */
    private $target;

    protected function setUp()
    {
        parent::setUp();

        $fkColumn = new MySqlColumn('col3', []);
        $fkColumn->setReference(new MySqlReference('target_table', 'target_column'));

        $columns = [
            'col1' => new MySqlColumn('col1', []),
            'col2' => new MySqlColumn('col2', []),
            'col3' => $fkColumn,
        ];

        $indexes = [
            new MySqlIndex('PRIMARY', ['col1', 'col2']),
            new MySqlIndex('index1', ['col2', 'col1']),
        ];

        $options = [
            'TABLE_CATALOG' => 'def',
            'TABLE_SCHEMA' => 'db',
            'TABLE_NAME' => 'table1',
            'TABLE_TYPE' => 'BASE TABLE',
            'ENGINE' => 'InnoDB',
            'VERSION' => 10,
            'ROW_FORMAT' => 'Dynamic',
            'TABLE_ROWS' => 8,
            'AVG_ROW_LENGTH' => 2048,
            'DATA_LENGTH' => 16384,
            'MAX_DATA_LENGTH' => 0,
            'INDEX_LENGTH' => 0, // TODO
            'DATA_FREE' => 0, // TODO
            'AUTO_INCREMENT' => 9,
            'CREATE_TIME' => '2019-04-24 21:29:54',
            'UPDATE_TIME' => '2019-04-25 08:12:33',
            'CHECK_TIME' => null, // TODO
            'TABLE_COLLATION' => 'utf8mb4_unicode_ci',
            'CHECKSUM' => null, // TODO
            'CREATE_OPTIONS' => '',
            'TABLE_COMMENT' => 'Table One',
        ];

        $table = new MySqlTable('testdb', $columns, $indexes, $options);

        $table->setConstraints([
            new MySqlConstraint('pk', ['col1', 'col2'], Constraint::TYPE_PRIMARY_KEY),
        ]);

        $this->target = $table;
    }

    public function testGetPrimaryKeys()
    {
        $pk = $this->target->getPrimaryKeys();
        $this->assertEquals(2, count($pk));
        $this->assertSame($this->target->getColumn('col1'), $pk[0]);
        $this->assertSame($this->target->getColumn('col2'), $pk[1]);
    }

    public function testGetForeignKeys()
    {
        $fk = $this->target->getForeignKeys();
        $this->assertEquals(1, count($fk));
        $this->assertSame('target_table', $fk['col3']->getReferencedTable());
        $this->assertSame('target_column', $fk['col3']->getReferencedColumn());
    }

    public function testGetComment()
    {
        $this->assertEquals('Table One', $this->target->getComment());
    }

    public function testGetters()
    {
        $this->assertEquals('InnoDB', $this->target->engine());
        $this->assertEquals(10, $this->target->version());
        $this->assertEquals('Dynamic', $this->target->rowFormat());
        $this->assertEquals(8, $this->target->tableRows());
        $this->assertEquals(2048, $this->target->avgRowLength());
        $this->assertEquals(16384, $this->target->dataLength());
        $this->assertEquals(0, $this->target->maxDataLength());
        $this->assertEquals(0, $this->target->indexLength());
        $this->assertEquals(0, $this->target->dataFree());
        $this->assertEquals(9, $this->target->autoIncrement());
        $this->assertEquals('2019-04-24 21:29:54', $this->target->createTime());
        $this->assertEquals('2019-04-25 08:12:33', $this->target->updateTime());
        $this->assertEquals(null, $this->target->checkTime());
        $this->assertEquals('utf8mb4_unicode_ci', $this->target->tableCollation());
        $this->assertEquals(null, $this->target->checksum());
        $this->assertEquals('', $this->target->createOptions());
    }
}