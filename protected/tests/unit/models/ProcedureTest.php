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


class ProcedureTest extends CDbTestCase
{
	public $model;

	public $fixtures = array(
		'procedures' => 'Procedure',
	);

	public function setUp()
	{
		parent::setUp();
		$this->model = new Procedure;
	}

	public function dataProvider_ProcedureSearch()
	{
		$procedure1 = array(
			1 => array(
				'term' => 'Foobar Procedure',
				'short_format' => 'FUB',
				'duration' => 60,
			)
		);
		$procedure2 = array(
			2 => array(
				'term' => 'Test Procedure',
				'short_format' => 'TP',
				'duration' => 20,
			)
		);

		return array(
			array('Foo', array('Foobar Procedure'), $procedure1),
			array('Foobar', array('Foobar Procedure'), $procedure1),
			array('Fo', array('Foobar Procedure'), $procedure1),
			array('Test', array('Test Procedure'), $procedure2),
			array('Test Pro', array('Test Procedure'), $procedure2),
			array('Te', array('Test Procedure'), $procedure2),
		);
	}

	public function testModel()
	{
		$this->assertEquals('Procedure', get_class(Procedure::model()));
	}

	public function testTableName()
	{
		$this->assertEquals('proc', $this->model->tableName());
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'id' => 'ID',
			'term' => 'Term',
			'short_format' => 'Short Format',
			'default_duration' => 'Default Duration',
		);

		$this->assertEquals($expected, $this->model->attributeLabels());
	}

	/**
	 * @dataProvider dataProvider_ProcedureSearch
	 */
	public function testGetList_ValidTerms_ReturnsValidResults($term, $data, $session)
	{
		Yii::app()->session['Procedures'] = null;
		$this->assertNull(Yii::app()->session['Procedures']);

		$results = Procedure::getList($term);

		$this->assertEquals($data, $results);
		$this->assertEquals($session, Yii::app()->session['Procedures']);
	}

	public function testGetList_CalledTwice_AppendsSessionData()
	{
		Yii::app()->session['Procedures'] = null;
		$this->assertNull(Yii::app()->session['Procedures']);

		$expected = array(
			1 => array(
				'term' => 'Foobar Procedure',
				'short_format' => 'FUB',
				'duration' => 60,
			),
			2 => array(
				'term' => 'Test Procedure',
				'short_format' => 'TP',
				'duration' => 20,
			)
		);

		$results = Procedure::getList('Fo');
		$this->assertEquals(array_slice($expected, 0, 1, true), Yii::app()->session['Procedures']);
		$results = Procedure::getList('Te');
		$this->assertEquals($expected, Yii::app()->session['Procedures']);
	}

	public function testGetList_InvalidTerm_ReturnsEmptyResults_SessionDataUnchanged()
	{
		Yii::app()->session['Procedures'] = null;
		$this->assertNull(Yii::app()->session['Procedures']);

		$expected = array(
			1 => array(
				'term' => 'Foobar Procedure',
				'short_format' => 'FUB',
				'duration' => 60,
			)
		);

		$results = Procedure::getList('Fo');
		$this->assertEquals($expected, Yii::app()->session['Procedures']);

		$results = Procedure::getList('Qux');
		$this->assertEquals(array(), $results);
		$this->assertEquals($expected, Yii::app()->session['Procedures']);
	}
}
