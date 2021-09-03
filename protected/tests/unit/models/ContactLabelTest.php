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
class ContactLabelTest extends ActiveRecordTestCase
{
    /**
     * @var ContactLabel
     */
    public $model;
    public $fixtures = array(
        'Contact',
        'contactlabels' => 'ContactLabel',
        'Institution',
    );

    public function getModel()
    {
        return ContactLabel::model();
    }

    public function dataProvider_Search()
    {
        return array(
            array(array('id' => 1, 'name' => 'contactlabel 1'), 1, array('contactlabel1')),
            array(array('id' => 2, 'name' => 'contactlabel 2'), 1, array('contactlabel2')),
        );
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();
        $this->model = new ContactLabel();
    }

    /**
     * @covers ContactLabel
     */
    public function testModel()
    {
        $this->assertEquals('ContactLabel', get_class(ContactLabel::model()), 'Class name should match model.');
    }

    /**
     * @covers ContactLabel
     */
    public function testTableName()
    {
        $this->assertEquals('contact_label', $this->model->tableName());
    }

    /**
     * @covers ContactLabel
     * @throws CException
     */
    public function testRules()
    {
        parent::testRules();
        $this->assertTrue($this->contactlabels('contactlabel1')->validate());
        $this->assertEmpty($this->contactlabels('contactlabel1')->errors);
    }

    /**
     * @covers ContactLabel
     */
    public function testAttributeLabels()
    {
        $expected = array(
            'id' => 'ID',
            'name' => 'Name',
            'letter_template_only' => 'Letter Template Only',
            'is_private' => 'Is Private',
            'max_number_per_patient' => 'Max Number Per Patient'
        );
        $this->assertEquals($expected, $this->model->attributeLabels());
    }

    /**
     * @covers ContactLabel
     * @dataProvider dataProvider_Search
     * @param $searchTerms
     * @param $numResults
     * @param $expectedKeys
     */
    public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
    {
        $contactlabel = new ContactLabel();
        $contactlabel->setAttributes($searchTerms);
        $results = $contactlabel->search();
        $data = $results->getData();

        $expectedResults = array();
        if (!empty($expectedKeys)) {
            foreach ($expectedKeys as $key) {
                $expectedResults[] = $this->contactlabels($key);
            }
        }
        $this->assertEquals($numResults, $results->getItemCount());
        $this->assertEquals($expectedResults, $data);
    }

    /**
     * @covers ContactLabel
     */
    public function testStaffType()
    {
        Yii::app()->session['selected_site_id'] = 1;

        $result = $this->model->staffType();
        $expected = 'Default staff';

        $this->assertEquals($expected, $result);
    }
}
