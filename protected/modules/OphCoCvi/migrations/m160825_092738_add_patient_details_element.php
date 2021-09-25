<?php
/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m160825_092738_add_patient_details_element extends OEMigration
{
    public function up()
    {
        $cviEvent = $this->insertOEEventType('CVI', 'OphCoCvi', 'Co');

        $this->insertOEElementType(array(
            'OEModule\\OphCoCvi\\models\\Element_OphCoCvi_Demographics' => array(
                'name' => 'Demographics',
                'required' => 1,
            ),
        ), $cviEvent);

        $this->createOETable(
            'et_ophcocvi_demographics',
            array(
                'id' => 'pk',
                'event_id' => 'int(10) unsigned',
                'name' => 'varchar(255)',
                'date_of_birth' => 'date',
                'nhs_number' => 'varchar(40)',
                'address' => 'text',
                'email' => 'varchar(255)',
                'telephone' => 'varchar(20)',
                'gender' => 'varchar(20)',
                'gp_name' => 'varchar(255)',
                'gp_address' => 'text',
                'gp_telephone' => 'varchar(20)',
            ),
            true
        );
    }

    public function down()
    {
        $this->delete('element_type', 'class_name = ? ', array('OEModule\\OphCoCvi\\models\\Element_OphCoCvi_Demographics'));
        $this->dropOETable('et_ophcocvi_demographics', true);
    }
}
