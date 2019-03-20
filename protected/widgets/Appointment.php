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


class Appointment extends BaseCWidget
{

    public $patient;
    public $past_worklist_patient_attributes;
    public $worklist_patients_attributes;
    public $pro_theme = '';

    public function init()
    {
        parent::init();

        $criteria = new \CDbCriteria();
        $criteria->join = " JOIN worklist_attribute wa ON t.worklist_attribute_id = wa.id";
        $criteria->join .= " JOIN worklist w ON w.id = wa.worklist_id";
        $criteria->join .= " JOIN worklist_patient wp ON wp.worklist_id=w.id AND wp.id = t.worklist_patient_id";
        $criteria->select = ['attribute_value', 'wp.when as time', 'w.name as worklistName', 'w.start as date'];
        $criteria->addCondition('wp.patient_id = :patient_id');
        $criteria->addCondition('wa.name = "Status"');
        $criteria->params[':patient_id'] = $this->patient->id;


        $criteria_past = clone $criteria;

        $criteria->addCondition('wp.when >= NOW()');
        $criteria->order = 'wp.when asc';

        $criteria_past->addCondition('wp.when < NOW()');
        $criteria_past->order = 'wp.when asc';

        $this->past_worklist_patient_attributes = WorklistPatientAttribute::model()->findAll($criteria_past);
        $this->worklist_patients_attributes = WorklistPatientAttribute::model()->findAll($criteria);
    }

    public function render($view, $data = null, $return = false)
    {
        if (is_array($data)) {
            $data = array_merge($data, get_object_vars($this));
        } else {
            $data = get_object_vars($this);
        }

        parent::render($view, $data, $return);
    }

    public function run()
    {
        $this->render(get_class($this));
    }
}