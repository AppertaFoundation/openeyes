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
class ContactTest extends CDbTestCase {

	public $model;
	public $fixtures = array(
		'contactlabels' => 'ContactLabel',
		'contacts' => 'Contact',
		'addresses' => 'Address',
		'contactlocations' => 'ContactLocation',
		'sites' => 'Site',
		'institutions' => 'Institution',
		'person' => 'Person'

      );

      public function dataProvider_Search() {
            return array(
                                     array(array('nick_name' => 'Aylward'), 1, array('contact1')),
                                     array(array('nick_name' => 'Collin'), 1, array('contact2')),
                                     array(array('nick_name' => 'Allan'), 1, array('contact3')),
                                     array(array('nick_name' => 'Blah'), 0, array()),
            );
      }

      /**
       * Sets up the fixture, for example, opens a network connection.
       * This method is called before a test is executed.
       */
      protected function setUp() {
            parent::setUp();
            $this->model = new Contact;
            //setup attributes to test with = contacts1
            $this->model->setAttributes($this->contacts('contact1')->getAttributes());
      }

	/**
	* Tears down the fixture, for example, closes a network connection.
	* This method is called after a test is executed.
	*/
	protected function tearDown() {
	}

      /**
       * @covers Contact::rules
       */
      public function testRules() {

            $this->assertTrue($this->contacts('contact1')->validate());
            $this->assertEmpty($this->contacts('contact1')->errors);
      }

      /**
       * @covers Contact::attributeLabels
       */
	public function testAttributeLabels() {
		$expected = array(
			 'id' => 'ID',
			 'nick_name' => 'Nickname',
			 'primary_phone' => 'Phone number',
			 'title' => 'Title',
			 'first_name' => 'First name',
			 'last_name' => 'Last name',
			 'qualifications' => 'Qualifications',
			 'contact_label_id' => 'Label',
		);

		$this->assertEquals($expected, $this->model->attributeLabels(), 'Attribute labels should match.');
	}

	/**
	* @covers Contact::getFullName
	*/
	public function testGetFullName()
	{

		$c1 = $this->contacts('contact1');

		$expected = trim(implode(' ',array($c1->title, $c1->first_name, $c1->last_name)));
		$result = $this->model->getFullName();

		$this->assertEquals($expected, $result);
	}

	/**
	* @covers Contact::getReversedFullName
	*/
	public function testGetReversedFullName()
	{
		$c1 = $this->contacts('contact1');

		$expected = trim(implode(' ',array($c1->title, $c1->last_name, $c1->first_name)));
		$result = $this->model->getReversedFullName();

		$this->assertEquals($expected, $result);
	}

	/**
	* @covers Contact::getSalutationName
	*/
	public function testGetSalutationName() {

		$c1 = $this->contacts('contact1');
		$expected = $c1->title . ' ' . $c1->last_name;
		$result = $this->model->GetSalutationName();

		$this->assertEquals($expected, $result);
	}

	/**
	 * @covers Contact::contactLine
	 */
	public function testContactLine_withLocation()
	{
		$c1 = $this->contacts('contact1');
		$location = $this->contactlocations('contactlocation1');
		$expectedwithlocation = $c1->getFullName() . ' (' . $c1->label->name . ', ' . $location . ')';

		$resultwithlocation = $this->model->ContactLine($location);
		$this->assertEquals($expectedwithlocation, $resultwithlocation);
	}

	/**
	 * @covers Contact::contactLine
	 */
	public function testContactLine_withoutLocation()
	{
		$c1 = $this->contacts('contact1');
		$expectedwithoutlocation = $c1->getFullName() . ' (' . $c1->label->name . ')';
		$resultwithoutlocation = $this->model->ContactLine();
		$this->assertEquals($expectedwithoutlocation, $resultwithoutlocation);
	}

	/**
	 * @covers Contact::findByLabel
	 */
	public function testFindByLabel_noPartialMatch()
	{
		$label = $this->contactlabels('contactlabel1');
		$res = Contact::model()->findByLabel('aylw', $label->name);

		$this->assertEquals(array(), $res, 'No partial match without % appended to search term');
	}

	/**
	 * @covers Contact::findByLabel
	 */
	public function testFindByLabel_wildcardMatchWithoutLocation()
	{
		$c4 = $this->contacts('contact4');
		$c5 = $this->contacts('contact5');
		$label = $c4->label;
		$term = strtolower(substr($c4->last_name, 0, 3));
		$res = Contact::model()->findByLabel($term . '%', $label->name);

		$expected = array(
			array('line' => $c5->ContactLine(), 'contact_id' => $c5->id),
			array('line' => $c4->ContactLine(), 'contact_id' => $c4->id),
		);

		$this->assertEquals($expected, $res, 'Should match the contact with wildcard appended to substring of last name');
	}
	/**
	 * @covers Contact::findByLabel
	 */
	public function testFindByLabel_wildcardMatchWithLocation()
	{
		$label = $this->contactlabels('contactlabel1');
		$res = Contact::model()->findByLabel('aylw%', $label->name);
		$c1 = $this->contacts('contact1');
		$expected = array(array('line' => $c1->ContactLine('City Road, flat 1, flitchley'), 'contact_location_id' => $this->contactlocations('contactlocation1')->id));

		$this->assertEquals($expected, $res, 'Should match the first contact with wildcard appended to term');
	}

	/**
	 * @covers Contact::findByLabel
	 */
	public function testFindByLabel_wildcardMatchPerson()
	{
		// note checking restricted to only Person as the search term matches a non-Person contact as well
		$c5 = $this->contacts('contact5');
		$term = strtolower(substr($c5->last_name, 0, 3));
		$expected = array(array('line' => $c5->ContactLine(), 'contact_id' => $c5->id));
		$res = Contact::model()->findByLabel($term . '%', $c5->label->name, false, 'person');

		$this->assertEquals($expected, $res);
	}

	/**
	 * @dataProvider dataProvider_Search
	 * @covers Contact::search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$contact = new Contact;
		$contact->setAttributes($searchTerms);
		$results = $contact->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->contacts($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount(), 'Number of results should match.');
		$this->assertEquals($expectedResults, $data, 'Actual results should match.');
	}

	/**
	 * @covers Contact::getType
	 */
	public function testGetType() {

		$this->model->setAttribute('id', 1);
		$result = $this->model->GetType();
		$expected = $this->contacts('contact1')->GetType();
		$this->assertEquals($expected, $result);
	}

}
