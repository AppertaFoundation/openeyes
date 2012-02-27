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

class m110412_134843_create_diagnosis_tables extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('disorder', 'id', 'int(10) unsigned NOT NULL AUTO_INCREMENT');
		$this->alterColumn('disorder', 'fully_specified_name', 'varchar(255) CHARACTER SET latin1 NOT NULL');
		$this->alterColumn('disorder', 'term', 'varchar(255) CHARACTER SET latin1 NOT NULL');

		$this->insert('disorder', array(
			'fully_specified_name' => 'Myopia (disorder)',
			'term' => 'Myopia',
			'systemic' => 0
		));
		$this->insert('disorder', array(
			'fully_specified_name' => 'Retinal lattice degeneration (disorder)',
			'term' => 'Retinal lattice degeneration',
			'systemic' => 0
		));
		$this->insert('disorder', array(
			'fully_specified_name' => 'Posterior vitreous detachment (disorder)',
			'term' => 'Posterior vitreous detachment',
			'systemic' => 0
		));
		$this->insert('disorder', array(
			'fully_specified_name' => 'Vitreous hemorrhage (disorder)',
			'term' => 'Vitreous haemorrhage',
			'systemic' => 0
		));
		$this->insert('disorder', array(
			'fully_specified_name' => 'Essential hypertension (disorder)',
			'term' => 'Essential hypertension',
			'systemic' => 1
		));
		$this->insert('disorder', array(
			'fully_specified_name' => 'Diabetes mellitus type 1 (disorder)',
			'term' => 'Diabetes mellitus type 1',
			'systemic' => 1
		));
		$this->insert('disorder', array(
			'fully_specified_name' => 'Diabetes mellitus type 2 (disorder)',
			'term' => 'Diabetes mellitus type 2',
			'systemic' => 1
		));
		$this->insert('disorder', array(
			'fully_specified_name' => 'Myocardial infarction (disorder)',
			'term' => 'Myocardial infarction',
			'systemic' => 1
		));

		$this->renameColumn('diagnosis', 'datetime', 'created_on');
		$this->renameColumn('diagnosis', 'site', 'location');

		$this->insert('diagnosis', array(
			'patient_id' => 1,
			'user_id' => 1,
			'disorder_id' => 1,
			'created_on' => '0000-00-00 00:00:00',
			'location' => 0
		));
		$this->insert('diagnosis', array(
			'patient_id' => 1,
			'user_id' => 1,
			'disorder_id' => 2,
			'created_on' => '0000-00-00 00:00:00',
			'location' => 1
		));
		$this->insert('diagnosis', array(
			'patient_id' => 1,
			'user_id' => 1,
			'disorder_id' => 3,
			'created_on' => '0000-00-00 00:00:00',
			'location' => 2
		));

		$this->createTable('common_ophthalmic_disorder', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'disorder_id' => 'int(10) unsigned NOT NULL',
			'specialty_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `disorder_id` (`disorder_id`)',
			'KEY `specialty_id` (`specialty_id`)'
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8');

		$this->insert('common_ophthalmic_disorder', array(
			'disorder_id' => 1, 'specialty_id' => 1
		));
		$this->insert('common_ophthalmic_disorder', array(
			'disorder_id' => 2, 'specialty_id' => 1
		));
		$this->insert('common_ophthalmic_disorder', array(
			'disorder_id' => 3, 'specialty_id' => 1
		));

		$this->addForeignKey(
			'common_ophthalmic_disorder_ibfk_1','common_ophthalmic_disorder','disorder_id','disorder','id');
		$this->addForeignKey(
			'common_ophthalmic_disorder_ibfk_2','common_ophthalmic_disorder','specialty_id','specialty','id');

		$this->createTable('common_systemic_disorder', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'disorder_id' => 'int(10) unsigned NOT NULL',
			'PRIMARY KEY (`id`)',
			'KEY `disorder_id` (`disorder_id`)',
		), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8');

		$this->insert('common_systemic_disorder', array('disorder_id' => 5));
		$this->insert('common_systemic_disorder', array('disorder_id' => 6));
		$this->insert('common_systemic_disorder', array('disorder_id' => 7));

		$this->addForeignKey(
			'common_systemic_disorder_ibfk_1','common_systemic_disorder','disorder_id','disorder','id');
	}

	public function down()
	{
		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 0;');
		$command->execute();

		$this->truncateTable('common_systemic_disorder');
		$this->dropTable('common_systemic_disorder');

		$this->truncateTable('common_ophthalmic_disorder');
		$this->dropTable('common_ophthalmic_disorder');

		$this->truncateTable('diagnosis');

		$this->truncateTable('disorder');

		$this->alterColumn('disorder', 'fully_specified_name', 'char(255) CHARACTER SET latin1 NOT NULL');
		$this->alterColumn('disorder', 'term', 'char(255) CHARACTER SET latin1 NOT NULL');

		$command = $this->dbConnection->createCommand('SET foreign_key_checks = 1;');
		$command->execute();
	}
}
