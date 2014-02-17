<?php

/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
class ContactLabelTest extends CDbTestCase
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
	protected function setUp()
	{
		parent::setUp();
		$this->model = new ContactLabel;
	}

	/**
	 * @covers ContactLabel::model
	 */
	public function testModel()
	{
		$this->assertEquals('ContactLabel', get_class(ContactLabel::model()), 'Class name should match model.');
	}

	/**
	 * @covers ContactLabel::tableName
	 */
	public function testTableName()
	{
		$this->assertEquals('contact_label', $this->model->tableName());
	}

	/**
	 * @covers ContactLabel::rules
	 */
	public function testRules()
	{
		$this->assertTrue($this->contactlabels('contactlabel1')->validate());
		$this->assertEmpty($this->contactlabels('contactlabel1')->errors);
	}

	/**
	 * @covers ContactLabel::attributeLabels
	 */
	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'name' => 'Name',
			'letter_template_only' => 'Letter Template Only',
		);
		$this->assertEquals($expected, $this->model->attributeLabels());
	}

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$contactlabel = new ContactLabel;
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
	 * @covers ContactLabel::staffType
	 */
	public function testStaffType()
	{
		Yii::app()->session['selected_site_id'] = 1;

		$result = $this->model->staffType();
		$expected = 'Moorfields staff';

		$this->assertEquals($expected, $result);
	}

}
