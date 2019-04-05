<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\widgets;

class Contacts extends \BaseEventElementWidget
{
    public static $moduleName = 'OphCiExamination';
    public $field_name = 'oe-attachment';

    // required
    public $event_id;
    public $form;
    public $element;

    public $contacts = [];

    /**
     * @return \Element_OphCiExamination_Contacts
     */
    protected function getNewElement()
    {
        return new \Element_OphCiExamination_Contacts();
    }

    public function init()
    {
        parent::init();

        $criteria = new \CDbCriteria();
        $criteria->addCondition('patient_id = '. $this->patient->id);
        $criteria->join = "JOIN patient_contact_assignment pca ON t.id = pca.contact_id";
        $this->contacts = \Contact::model()->findAll($criteria);
//       \PatientContactAssignment::model()->findAll("patient_id = ? AND" $this->patient->id);
    }

}