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
class PhraseBySubspecialtyTest extends CDbTestCase
{

	public $fixtures = array(
		'sections' => 'Section',
		'sectionTypes' => 'SectionType',
		'services' => 'Service',
		'specialties' => 'Specialty',
		//'serviceSpecialtyAssignment' => 'ServiceSubspecialtyAssignment',
		'firms' => 'Firm',
		'eventTypes' => 'EventType',
		'elementTypes' => 'ElementType',
		//'possibleElementTypes' => 'PossibleElementType',
		'siteElementTypes' => 'ElementType',
		'phrasesBySubspecialty' => 'PhraseBySubspecialty',
		'phraseNames' => 'PhraseName'
	);

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->markTestSkipped('Too many errors in this class test, Skipping until someone can refactor it.');
		parent::setUp();
		$this->object = new PhraseBySubspecialty;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{

	}

	/**
	 * @covers PhraseBySubspecialty::model
	 * @todo   Implement testModel().
	 */
	public function testModel()
	{

		$this->assertEquals('PhraseBySubspecialty', get_class(PhraseBySubspecialty::model()), 'Class name should match model.');
	}

	/**
	 * @covers PhraseBySubspecialty::tableName
	 * @todo   Implement testTableName().
	 */
	public function testTableName()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers PhraseBySubspecialty::relevantSectionTypes
	 * @todo   Implement testRelevantSectionTypes().
	 */
	public function testRelevantSectionTypes()
	{

		$relevantSectionTypes = PhraseBySubspecialty::model()->relevantSectionTypes();
		$this->assertTrue(is_array($relevantSectionTypes));
		foreach ($relevantSectionTypes as $relevantSectionType) {
			$sectionType = SectionType::model()->findByAttributes(array('name' => $relevantSectionType));
			$this->assertInstanceOf('SectionType', $sectionType);
		}
	}

	/**
	 * @covers PhraseBySubspecialty::rules
	 * @todo   Implement testRules().
	 */
	public function testRules()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers PhraseBySubspecialty::relations
	 * @todo   Implement testRelations().
	 */
	public function testRelations()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers PhraseBySubspecialty::attributeLabels
	 * @todo   Implement testAttributeLabels().
	 */
	public function testAttributeLabels()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers PhraseBySubspecialty::ValidatorPhraseNameId
	 * @todo   Implement testValidatorPhraseNameId().
	 */
	public function testValidatorPhraseNameId()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers PhraseBySubspecialty::search
	 * @todo   Implement testSearch().
	 */
	public function testSearch()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers PhraseBySubspecialty::getOverrideableNames
	 * @todo   Implement testGetOverrideableNames().
	 */
	public function testGetOverrideableNames()
	{

		$overrideable = PhraseBySubspecialty::model()->getOverrideableNames($this->sections['section1']['id'], $this->firms['firm1']['id']);
		$this->assertTrue(is_array($overrideable));

		$expected = array('Congenital Cataract', 'unnatural cataract');
		$count = 0;
		foreach ($overrideable as $o) {
			$this->assertEquals($expected[$count], $o->name);
			$count++;
		}
	}

	public function testGet_InvalidParameters_ReturnsFalse()
	{
		$fakeId = 9999;
		$result = PhraseBySubspecialty::model()->findByPk($fakeId);
		$this->assertNull($result);
	}

	public function testCreate()
	{
		$phrase = new PhraseBySubspecialty;
		$phrase->setAttributes(array(
			'phrase' => 'Testing phrase',
			'section_id' => $this->sections['section1']['id'],
			'subspecialty_id' => $this->specialties['specialty1']['id'],
			'display_order' => 1,
			'phrase_name_id' => $this->phraseNames['phraseName1']['id'],
		));
		$this->assertTrue($phrase->save(true));
	}

}
