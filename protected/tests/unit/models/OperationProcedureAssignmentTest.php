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


class OperationProcedureAssignmentTest extends CDbTestCase
{
	public $model;

	public $fixtures = array(
		'procedures' => 'Procedure',
		'operations' => 'ElementOperation',
		'operationProcedures' => 'OperationProcedureAssignment'
	);

	public function setUp()
	{
		parent::setUp();
		$this->model = new OperationProcedureAssignment;
	}

	public function testModel()
	{
		$this->assertEquals('OperationProcedureAssignment', get_class(OperationProcedureAssignment::model()));
	}

	public function testTableName()
	{
		$this->assertEquals('operation_procedure_assignment', $this->model->tableName());
	}

	public function testAttributeLabels()
	{
		$expected = array(
			'operation_id' => 'Operation',
			'proc_id' => 'Procedure',
			'display_order' => 'Display Order',
		);

		$this->assertEquals($expected, $this->model->attributeLabels());
	}

	public function testPrimaryKey()
	{
		$expected = array('operation_id', 'proc_id');
		$this->assertEquals($expected, $this->model->primaryKey());
	}

	public function testDefaultScope()
	{
		$expected = array('order'=>'display_order ASC');
		$this->assertEquals($expected, $this->model->defaultScope());
	}
}