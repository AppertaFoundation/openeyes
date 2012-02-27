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

class m110703_154341_create_patient_contact extends CDbMigration
{
	public function up()
	{
                $this->createTable('patient_contact_assignment', array(
                        'patient_id' => 'int(10) unsigned NOT NULL',
                        'contact_id' => 'int(10) unsigned NOT NULL',
                        'PRIMARY KEY (`patient_id`, `contact_id`)',
                        'KEY `patient_id` (`patient_id`)',
                        'KEY `contact_id` (`contact_id`)',
                ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

                $this->addForeignKey(
                        'patient_contact_assignment_fk_1','patient_contact_assignment','patient_id','patient','id');
                $this->addForeignKey(
                        'patient_contact_assignment_fk_2','patient_contact_assignment','contact_id','contact','id');

                $this->insert('patient_contact_assignment', array(
                        'patient_id' => 1,
                        'contact_id' => 1
                ));

                $this->insert('patient_contact_assignment', array(
                        'patient_id' => 2,
                        'contact_id' => 1
                ));

                $this->insert('patient_contact_assignment', array(
                        'patient_id' => 3,
                        'contact_id' => 1
                ));

		$this->addColumn('element_letterout', 'cc', 'TEXT');
	}

	public function down()
	{
		$this->dropForeignKey('patient_contact_assignment_fk_1', 'patient_contact_assignment');
		$this->dropForeignKey('patient_contact_assignment_fk_2', 'patient_contact_assignment');

		$this->dropTable('patient_contact_assignment');

		$this->dropColumn('element_letterout', 'cc');
	}
}
