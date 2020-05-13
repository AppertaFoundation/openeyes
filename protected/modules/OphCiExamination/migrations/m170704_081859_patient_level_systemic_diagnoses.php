<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class m170704_081859_patient_level_systemic_diagnoses extends OEMigration
{
    public function up()
    {
        $this->createElementType('OphCiExamination', 'Systemic Diagnoses', array(
            'class_name' => 'OEModule\OphCiExamination\models\SystemicDiagnoses',
            'display_order' => 5,
            'parent_class' => 'OEModule\OphCiExamination\models\Element_OphCiExamination_History',
        ));

        $this->createOETable('et_ophciexamination_systemic_diagnoses', array(
            'id' => 'pk',
            'event_id' => 'int(10) unsigned NOT NULL',
        ), true);

        $this->addForeignKey(
            'et_ophciexamination_sysdiag_ev_fk',
            'et_ophciexamination_systemic_diagnoses',
            'event_id',
            'event',
            'id'
        );

        $this->createOETable('ophciexamination_systemic_diagnoses_diagnosis', array(
            'id' => 'pk',
            'element_id' => 'int(11) NOT NULL',
            'side_id' => 'int(10) unsigned',
            'disorder_id' => 'BIGINT unsigned NOT NULL',
            'date' => 'varchar(10)',
            'secondary_diagnosis_id' => 'int(10) unsigned'
        ), true);

        $this->addForeignKey(
            'ophciexamination_sysdiag_dia_el_fk',
            'ophciexamination_systemic_diagnoses_diagnosis',
            'element_id',
            'et_ophciexamination_systemic_diagnoses',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_sysdiag_dia_side_fk',
            'ophciexamination_systemic_diagnoses_diagnosis',
            'side_id',
            'eye',
            'id'
        );
        $this->addForeignKey(
            'ophciexamination_sysdiag_dia_dis_fk',
            'ophciexamination_systemic_diagnoses_diagnosis',
            'disorder_id',
            'disorder',
            'id'
        );
        // set null on delete of SD - simple means of resolving whether still in sync or not.
        $this->addForeignKey(
            'ophciexamination_sysdiag_dia_sd_fk',
            'ophciexamination_systemic_diagnoses_diagnosis',
            'secondary_diagnosis_id',
            'secondary_diagnosis',
            'id',
            'SET NULL'
        );

    }

    public function down()
    {
        $this->dropOETable('ophciexamination_systemic_diagnoses_diagnosis', true);
        $this->dropOETable('et_ophciexamination_systemic_diagnoses', true);
        $id = $this->getIdOfElementTypeByClassName('OEModule\OphCiExamination\models\SystemicDiagnoses');
        $this->delete('element_type', 'id = ?', array($id));
    }

}
