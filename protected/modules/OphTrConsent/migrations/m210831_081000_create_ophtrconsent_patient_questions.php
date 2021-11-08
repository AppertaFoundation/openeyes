<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class m210831_081000_create_ophtrconsent_patient_questions extends OEMigration
{

    public function up()
    {
        Yii::app()->cache->flush();

        $this->createOETable("et_ophtrconsent_patientquestions", array(
            "id" => "pk",
            "event_id" => "int(10) unsigned NOT NULL",
            "questions" => "text",
            "refused_procedures" => "text",
            "CONSTRAINT `et_ophtrconsent_patientquestions_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)"

        ), true);

        $element_types = array(
            'Element_OphTrConsent_PatientQuestions' => array('name' => 'Patient Questions', 'display_order' => 41),
        );

        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where(
            'class_name = :class_name',
            array(':class_name' => 'OphTrConsent')
        )->queryScalar();

        $this->insertOEElementType($element_types, $event_type_id);
    }

    public function down()
    {
        $element_types = array(
            'Element_OphTrConsent_PatientQuestions' => array('name' => 'Patient Questions', 'display_order' => 41),
        );

        $event_type_id = $this->dbConnection->createCommand()->select('id')->from('event_type')->where(
            'class_name = :class_name',
            array(':class_name' => 'OphTrConsent')
        )->queryScalar();

        $this->deleteElementType($event_type_id, $element_types);
        $this->dropOETable('et_ophtrconsent_patientquestions', true);
    }
}
