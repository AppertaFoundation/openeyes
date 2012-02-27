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

class m120222_115209_new_general_anaesthetic_field_for_sessions_and_sequences extends CDbMigration
{
	public function up()
	{
		$this->addColumn('sequence','general_anaesthetic','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('session','general_anaesthetic','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->update('sequence',array('general_anaesthetic' => 1), 'anaesthetist = 1');
		$this->update('session',array('general_anaesthetic' => 1), 'anaesthetist = 1');

		$sequences = array();

		// Anaesthetists at St Anns, Mile End and Potters Bar cannot do general anaesthetic
		foreach ($this->dbConnection->createCommand()
			->select('sequence.id')
			->from('sequence')
			->join('theatre','theatre.id = sequence.theatre_id')
			->join('site','site.id = theatre.site_id')
			->where("site.short_name in ('St Ann''s','Mile End','Potters Bar')")
			->queryAll() as $row) {
			$sequences[] = $row['id'];
		}

		if (!empty($sequences)) {
			$this->update('sequence',array('general_anaesthetic' => 0),'id in ('.implode(',',$sequences).')');
			$this->update('session',array('general_anaesthetic' => 0),'sequence_id in ('.implode(',',$sequences).')');
		}
	}

	public function down()
	{
		$this->dropColumn('sequence','general_anaesthetic');
		$this->dropColumn('session','general_anaesthetic');
	}
}
