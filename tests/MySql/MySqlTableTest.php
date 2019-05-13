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

    public function getComment()
    {
        $this->assertEquals('Table One', $this->target->getComment());
    }
}