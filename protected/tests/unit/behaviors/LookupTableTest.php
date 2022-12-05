<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class LookupTableTest extends OEDbTestCase
{
    public function setUp(): void
    {
        $this->getFixtureManager()->dbConnection->createCommand(
            'create temporary table test_lookup_table (id int unsigned primary key, name varchar(63), active tinyint(1) unsigned not null default 1) engine=innodb'
        )->execute();

        $this->getFixtureManager()->dbConnection->commandBuilder->createMultipleInsertCommand(
            'test_lookup_table',
            array(
                array('id' => 1, 'name' => 'foo', 'active' => true),
                array('id' => 2, 'name' => 'bar', 'active' => false),
                array('id' => 3, 'name' => 'baz', 'active' => false),
                array('id' => 4, 'name' => 'qux', 'active' => true),
            )
        )->execute();
    }

    public function tearDown(): void
    {
        $this->getFixtureManager()->dbConnection->createCommand('drop temporary table test_lookup_table')->execute();
    }

    /**
     * @covers LookupTable
     */
    public function testActive()
    {
        $results = LookupTableTest_TestClass::model()->active()->findAll();
        $this->assertCount(2, $results);
        $this->assertEquals('foo', $results[0]->name);
        $this->assertEquals('qux', $results[1]->name);
    }

    /**
     * @covers LookupTable
     */
    public function testActiveOrPkWithNull()
    {
        $results = LookupTableTest_TestClass::model()->activeOrPk(null)->findAll();
        $this->assertCount(2, $results);
        $this->assertEquals('foo', $results[0]->name);
        $this->assertEquals('qux', $results[1]->name);
    }

    /**
     * @covers LookupTable
     */
    public function testActiveOrPkWithScalar()
    {
        $results = LookupTableTest_TestClass::model()->activeOrPk(2)->findAll();
        $this->assertCount(3, $results);
        $this->assertEquals('foo', $results[0]->name);
        $this->assertEquals('bar', $results[1]->name);
        $this->assertEquals('qux', $results[2]->name);
    }

    /**
     * @covers LookupTable
     */
    public function testActiveOrPkWithArray()
    {
        $results = LookupTableTest_TestClass::model()->activeOrPk(array(2, 3))->findAll();
        $this->assertCount(4, $results);
        $this->assertEquals('foo', $results[0]->name);
        $this->assertEquals('bar', $results[1]->name);
        $this->assertEquals('baz', $results[2]->name);
        $this->assertEquals('qux', $results[3]->name);
    }

    /**
     * @covers LookupTable
     */
    public function testActiveOrPkWithArrayContainingNull()
    {
        $results = LookupTableTest_TestClass::model()->activeOrPk(array(2, null))->findAll();
        $this->assertCount(3, $results);
        $this->assertEquals('foo', $results[0]->name);
        $this->assertEquals('bar', $results[1]->name);
        $this->assertEquals('qux', $results[2]->name);
    }
}

class LookupTableTest_TestClass extends BaseActiveRecord
{
    public function tableName()
    {
        return 'test_lookup_table';
    }

    public function behaviors()
    {
        return array('LookupTable' => 'LookupTable');
    }

    public function defaultScope()
    {
        return array('order' => 'id');
    }
}
