<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor\MySql;


use Yokuru\DbDescriptorTests\TestCase;

class MySqlColumnTest extends TestCase
{
    /**
     * @var MySqlColumn
     */
    private $target;

    protected function setUp()
    {
        parent::setUp();

        $options = [
            'TABLE_CATALOG' => 'def',
            'TABLE_SCHEMA' => 'db',
            'TABLE_NAME' => 'table1',
            'COLUMN_NAME' => 'col1',
            'ORDINAL_POSITION' => 1,
            'COLUMN_DEFAULT' => null,
            'IS_NULLABLE' => 'NO',
            'DATA_TYPE' => 'int',
            'CHARACTER_MAXIMUM_LENGTH' => null, // TODO
            'CHARACTER_OCTET_LENGTH' => null, // TODO
            'NUMERIC_PRECISION' => 10,
            'NUMERIC_SCALE' => 0,
            'DATETIME_PRECISION' => null, // TODO
            'CHARACTER_SET_NAME' => null, // TODO
            'COLLATION_NAME' => null, // TODO
            'COLUMN_TYPE' => 'int(10) unsigned',
            'COLUMN_KEY' => 'PRI',
            'EXTRA' => 'auto_increment',
            'PRIVILEGES' => 'select,insert,update,references',
            'COLUMN_COMMENT' => 'ID',
            'GENERATION_EXPRESSION' => '',
        ];

        $this->target = new MySqlColumn('col1', $options);
    }

    public function testGetters()
    {
        $this->assertEquals('def', $this->target->tableCatalog());
        $this->assertEquals('db', $this->target->tableSchema());
        $this->assertEquals('table1', $this->target->tableName());
        $this->assertEquals('col1', $this->target->columnName());
        $this->assertEquals(1, $this->target->ordinalPosition());
        $this->assertEquals(null, $this->target->columnDefault());
        $this->assertEquals('NO', $this->target->isNullable());
        $this->assertEquals('int', $this->target->dataType());
        $this->assertEquals(null, $this->target->characterMaximumLength());
        $this->assertEquals(null, $this->target->characterOctetLength());
        $this->assertEquals(10, $this->target->numericPrecision());
        $this->assertEquals(0, $this->target->numericScale());
        $this->assertEquals(null, $this->target->datetimePrecision());
        $this->assertEquals(null, $this->target->characterSetName());
        $this->assertEquals(null, $this->target->collationName());
        $this->assertEquals('int(10) unsigned', $this->target->columnType());
        $this->assertEquals('PRI', $this->target->columnKey());
        $this->assertEquals('auto_increment', $this->target->extra());
        $this->assertEquals('select,insert,update,references', $this->target->privileges());
        $this->assertEquals('ID', $this->target->columnComment());
        $this->assertEquals('', $this->target->generationExpression());
    }


}