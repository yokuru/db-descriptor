<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor\MySql;

use Yokuru\DbDescriptorTests\TestCase;

class MySqlColumnTest extends TestCase
{

    public function testConstructor()
    {
        $column = new MySqlColumn('col1', [
            'EXTRA' => 'auto_increment',
            'COLUMN_TYPE' => 'int(10) unsigned',
            'IS_NULLABLE' => 'NO',
        ]);
        $this->assertTrue($column->isAutoIncrement());
        $this->assertTrue($column->isUnsigned());
        $this->assertTrue($column->isNotNull());

        $column = new MySqlColumn('col2', [
            'EXTRA' => '',
            'COLUMN_TYPE' => 'int(10)',
            'IS_NULLABLE' => 'YES',
        ]);
        $this->assertFalse($column->isAutoIncrement());
        $this->assertFalse($column->isUnsigned());
        $this->assertFalse($column->isNotNull());

        $column = new MySqlColumn('col3', [
            'DATA_TYPE' => 'enum',
            'COLUMN_TYPE' => "enum('A','B','C')",
        ]);
        $enum = $column->getEnumValues();
        $this->assertEquals(3, count($enum));
        $this->assertSame(['A', 'B', 'C'], $enum);
    }

    public function testGetters()
    {
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

        $column = new MySqlColumn('col1', $options);

        $this->assertEquals('def', $column->tableCatalog());
        $this->assertEquals('db', $column->tableSchema());
        $this->assertEquals('table1', $column->tableName());
        $this->assertEquals('col1', $column->columnName());
        $this->assertEquals(1, $column->ordinalPosition());
        $this->assertEquals(null, $column->columnDefault());
        $this->assertEquals('NO', $column->isNullable());
        $this->assertEquals('int', $column->dataType());
        $this->assertEquals(null, $column->characterMaximumLength());
        $this->assertEquals(null, $column->characterOctetLength());
        $this->assertEquals(10, $column->numericPrecision());
        $this->assertEquals(0, $column->numericScale());
        $this->assertEquals(null, $column->datetimePrecision());
        $this->assertEquals(null, $column->characterSetName());
        $this->assertEquals(null, $column->collationName());
        $this->assertEquals('int(10) unsigned', $column->columnType());
        $this->assertEquals('PRI', $column->columnKey());
        $this->assertEquals('auto_increment', $column->extra());
        $this->assertEquals('select,insert,update,references', $column->privileges());
        $this->assertEquals('ID', $column->columnComment());
        $this->assertEquals('', $column->generationExpression());
    }

    public function testParseEnumValues()
    {
        $columnType = "enum('T1','T2','T3')";
        $this->assertSame([
            'T1',
            'T2',
            'T3',
        ], MySqlColumn::parseEnumValues($columnType));

        $columnType = "enum('AAA','B''BB','C\"CC','''D,DD','\"E,EE')";
        $this->assertSame([
            'AAA',
            "B'BB",
            'C"CC',
            "'D,DD",
            '"E,EE',
        ], MySqlColumn::parseEnumValues($columnType));
    }

}