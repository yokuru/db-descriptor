<?php
declare(strict_types=1);

namespace Yokuru\DbDescriptor\MySql;

use Yokuru\DbDescriptorTests\TestCase;

class MySqlDescriptorTest extends TestCase
{

    /**
     * @var MySqlDescriptor
     */
    private $target;

    protected function setUp()
    {
        parent::setUp();
        $this->target = new MySqlDescriptor($this->getConnection());
    }


    public function testDescribeTable()
    {
        $db = $this->target->describeDatabase();
        $this->assertEquals(getenv('DB_NAME'), $db->getName());
    }



}