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
class AddressTest extends CDbTestCase
{
    public $model;

    public $fixtures = array(
        'patients' => 'Patient',
        'addresses' => 'Address',
    );

    public function dataProvider_Search()
    {
        return array(
            array(array('address1' => 'flat 1'), 1, array('address1')),
            //array(array('address1' => 'FLAT 1'), 1, array('address1')), /* case insensitivity test */
            array(array('address2' => 'bleakley'), 3, array('address1', 'address2', 'address3')),
            array(array('city' => 'flitchley'), 3, array('address1', 'address2', 'address3')),
            array(array('postcode' => 'ec1v'), 3, array('address1', 'address2', 'address3')),
            array(array('county' => 'london'), 3, array('address1', 'address2', 'address3')),
            array(array('email' => 'bleakley1'), 1, array('address1')),
            array(array('email' => 'foobar'), 0, array()),
        );
    }

    public function setUp()
    {
        parent::setUp();
        $this->model = new Address();
    }

    public function testModel()
    {
        $this->assertEquals('Address', get_class(Address::model()), 'Class name should match model.');
    }

    public function testAttributeLabels()
    {
        $expected = array(
            'id' => 'ID',
            'address1' => 'Address1',
            'address2' => 'Address2',
            'city' => 'City',
            'postcode' => 'Postcode',
            'county' => Yii::app()->params['county_label'],
            'country_id' => 'Country',
            'email' => 'Email',
        );

        $this->assertEquals($expected, $this->model->attributeLabels());
    }

    /**
     * @covers AuditTrail::rules
     *
     * @todo   Implement testRules().
     */
    public function testRules()
    {
        $this->assertTrue($this->addresses('address3')->validate());
        $this->assertEmpty($this->addresses('address3')->errors);
    }

    /**
     * @covers Address::isCurrent
     *
     * @todo   Implement testIsCurrent().
     */
    public function testIsCurrent()
    {
        $this->assertTrue($this->model->isCurrent());
    }

    /**
     * @covers Address::getLetterLine
     *
     * @todo   Implement testGetLetterLine().
     */
    public function testGetLetterLine()
    {
        $expected = $this->addresses('address3')->getLetterLine($include_country = false);

        $this->model->setAttributes($this->addresses('address3')->getAttributes());

        $this->assertEquals($expected, $this->model->getLetterLine($include_country = false));
    }

    /**
     * @covers Address::getSummary
     *
     * @todo   Implement testGetSummary().
     */
    public function testGetSummary()
    {

        //set attributes
        $this->model->setAttributes($this->addresses('address3')->getAttributes());

        $this->assertEquals($this->addresses('address3')->address1, $this->model->GetSummary());
    }

    /**
     * @covers Address::getLetterArray
     *
     * @todo   Implement testGetLetterArray().
     */
    public function testGetLetterArray()
    {
        $expected = array(
            0 => '1',
            1 => 'flat 1',
            2 => 'bleakley creek',
            3 => 'flitchley',
            4 => 'london',
            5 => 'ec1v 0dx',
        );

        $this->model->setAttributes($this->addresses('address1')->getAttributes());

        $this->assertEquals($expected, $this->model->getLetterArray($include_country = false, $name = true));
    }

    /**
     * @dataProvider dataProvider_Search
     */
    public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
    {
        $address = new Address();
        $address->setAttributes($searchTerms);
        $results = $address->search();
        $data = $results->getData();

        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->addresses($key);
            }
        }

        $this->assertEquals($numResults, $results->getItemCount());
        $this->assertEquals($expectedResults, $data);
    }
}
