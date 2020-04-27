<?php
/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2019
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m190613_004744_add_multiple_contacts_to_patient extends OEMigration
{

    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
        $this->createOETable('patient_contact_associate', array(
            'id' => 'pk',
            'patient_id' => 'int(10) unsigned NOT NULL',
            'gp_id' => 'int(10) unsigned NOT NULL',
        ), true);

        $this->addForeignKey('patient_contact_associate_patient_fk', 'patient_contact_associate', 'patient_id', 'patient', 'id');
        $this->addForeignKey('patient_contact_associate_gp_fk', 'patient_contact_associate', 'gp_id', 'gp', 'id');

        $this->createOETable('contact_practice_associate', array(
            'id' => 'pk',
            'gp_id' => 'int(10) unsigned NOT NULL',
            'practice_id' => 'int(10) unsigned NOT NULL',
        ), true);

        $this->addForeignKey('contact_practice_associate_practice_fk', 'contact_practice_associate', 'practice_id', 'practice', 'id');
        $this->addForeignKey('contact_practice_associate_gp_fk', 'contact_practice_associate', 'gp_id', 'gp', 'id');

    }

    public function safeDown()
    {
        $this->dropForeignKey('patient_contact_associate_patient_fk', 'patient_contact_associate');
        $this->dropForeignKey('patient_contact_associate_gp_fk', 'patient_contact_associate');

        $this->dropForeignKey('contact_practice_associate_practice_fk', 'contact_practice_associate');
        $this->dropForeignKey('contact_practice_associate_gp_fk', 'contact_practice_associate');


        $this->dropOETable('patient_contact_associate', true);
        $this->dropOETable('contact_practice_associate', true);

    }
}
