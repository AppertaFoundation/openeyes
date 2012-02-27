<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class m110414_090524_remove_segment_drawing_data extends CDbMigration
{
	public function up()
	{
		// retrieve the element_type id's for these classnames
		$this->delete('element_type', 'class_name = :classname', array(':classname' => 'ElementAnteriorSegmentDrawing'));
		$this->delete('element_type', 'class_name = :classname', array(':classname' => 'ElementPosteriorSegmentDrawing'));
	}

	public function down()
	{
		$this->insert('element_type',
			array(
				'name' => 'Anterior Segment Drawing',
				'class_name' => 'ElementAnteriorSegmentDrawing',
			)
		);
		$this->insert('element_type',
			array(
				'name' => 'Posterior Segment Drawing',
				'class_name' => 'ElementPosteriorSegmentDrawing',
			)
		);
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}
