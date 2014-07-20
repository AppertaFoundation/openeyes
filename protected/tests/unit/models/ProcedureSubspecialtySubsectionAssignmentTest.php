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
class ProcedureSpecialtySubsectionAssignmentTest extends CDbTestCase
{

	public $model;
	public $fixtures = array(
			'procedures' => 'Procedure',
			'specialties' => 'Specialty',
			'subsections' => 'SubspecialtySubsection',
			'assignments' => 'ProcedureSubspecialtySubsectionAssignment'
	);

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->model = new ProcedureSubspecialtySubsectionAssignment;
	}

	public function dataProvider_Search()
	{
		return array(
				array(array('proc_id' => 1), 1, array('pssa1')),
				array(array('specialty_subsection_id' => 1), 1, array('pssa1')),
		);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{

	}

	/**
	 * @covers ProcedureSubspecialtySubsectionAssignment::model
	 * @todo   Implement testModel().
	 */
	public function testModel()
	{
		$this->assertEquals('ProcedureSubspecialtySubsectionAssignment', get_class(ProcedureSubspecialtySubsectionAssignment::model()));
	}

	/**
	 * @covers ProcedureSubspecialtySubsectionAssignment::tableName
	 * @todo   Implement testTableName().
	 */
	public function testTableName()
	{
		$this->assertEquals('proc_subspecialty_subsection_assignment', $this->model->tableName());
	}

	/**
	 * @covers ProcedureSubspecialtySubsectionAssignment::rules
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
	 * @covers ProcedureSubspecialtySubsectionAssignment::relations
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
	 * @covers ProcedureSubspecialtySubsectionAssignment::attributeLabels
	 * @todo   Implement testAttributeLabels().
	 */
	public function testAttributeLabels()
	{
		$expected = array(
				'id' => 'ID',
				'proc_id' => 'Proc',
				'subspecialty_subsection_id' => 'Subspecialty Subsection',
		);

		$this->assertEquals($expected, $this->model->attributeLabels());
	}

	/**
	 * @covers ProcedureSubspecialtySubsectionAssignment::search
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
	 * @dataProvider dataProvider_Search
	 */
	public function testSearch_WithValidTerms_ReturnsExpectedResults($searchTerms, $numResults, $expectedKeys)
	{
		$assignment = new ProcedureSubspecialtySubsectionAssignment;
		$assignment->setAttributes($searchTerms);
		$results = $assignment->search();
		$data = $results->getData();

		$expectedResults = array();
		if (!empty($expectedKeys)) {
			foreach ($expectedKeys as $key) {
				$expectedResults[] = $this->assignments($key);
			}
		}

		$this->assertEquals($numResults, $results->getItemCount(), 'Number of results should match.');
		$this->assertEquals($expectedResults, $data, 'Actual results should match.');
	}

}
