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
class AllergyTest extends CDbTestCase
{
    /**
     * @var AddressType
     */
    public $model;
    public $fixtures = array(
        'allergy' => 'Allergy',
    );

    public function dataProvider_Search()
    {
        return array(
            array(array('id' => 1, 'name' => 'allergy 1'), 1, array('allergy1')),
            array(array('id' => 2, 'name' => 'allergy 2'), 1, array('allergy2')),
        );
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->model = new Allergy();
    }

    /**
     * @covers Allergy::model
     */
    public function testModel()
    {
        $this->assertEquals('Allergy', get_class(Allergy::model()), 'Class name should match model.');
    }

    /**
     * @covers Allergy::tableName
     */
    public function testTableName()
    {
        $this->assertEquals('allergy', $this->model->tableName());
    }

    /**
     * @covers Allergy::attributeLabels
     */
    public function testAttributeLabels()
    {
        $expected = array();
        $this->assertEquals($expected, $this->model->attributeLabels());
    }

    /**
     * @dataProvider dataProvider_Search
     */
    public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
    {
        $allergy = new Allergy();
        $allergy->setAttributes($searchTerms);
        $allergyresults = $allergy->search();
        $allergydata = $allergyresults->getData();

        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->allergy($key);
            }
        }

        $this->assertEquals($numResults, $allergyresults->getItemCount());
        $this->assertEquals($expectedResults, $allergydata);
    }
}
