<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class AddressTypeTest
 * @method addresstype($fixtureId)
 */
class AddressTypeTest extends ActiveRecordTestCase
{
    public function getModel()
    {
        return AddressType::model();
    }

    /**
     * @var AddressType
     */
    public AddressType $model;

    public $fixtures = array(
        'addresstype' => 'AddressType',
    );

    public function dataProvider_Search()
    {
        return array(
            array(array('id' => 1, 'name' => 'addresstype 1'), 1, array('addresstype1')),
            array(array('id' => 2, 'name' => 'addresstype 2'), 1, array('addresstype2')),
        );
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->model = new AddressType();
    }

    /**
     * @covers AddressType
     */
    public function testModel()
    {
        $this->assertEquals('AddressType', get_class(AddressType::model()), 'Class name should match model.');
    }

    /**
     * @covers AddressType
     */
    public function testTableName()
    {
        $this->assertEquals('address_type', $this->model->tableName());
    }

    /**
     * @covers AddressType
     * @throws CException
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->addresstype('addresstype1')->validate());
        $this->assertEmpty($this->addresstype('addresstype1')->errors);
    }

    /**
     * @covers AddressType
     */
    public function testAttributeLabels()
    {
        $expected = array();

        $this->assertEquals($expected, $this->model->attributeLabels());
    }

    /**
     * @covers       AddressType
     * @dataProvider dataProvider_Search
     * @param $searchTerms
     * @param $numResults
     * @param $expectedKeys
     */
    public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
    {
        $addresstype = new AddressType();
        $addresstype->setAttributes($searchTerms);
        $addresstyperesults = $addresstype->search();
        $addresstypedata = $addresstyperesults->getData();

        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->addresstype($key);
            }
        }

        $this->assertEquals($numResults, $addresstyperesults->getItemCount());
        $this->assertEquals($expectedResults, $addresstypedata);
    }
}
