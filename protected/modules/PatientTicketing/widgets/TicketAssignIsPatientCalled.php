<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PatientTicketing\widgets;

use OEModule\PatientTicketing\components\AutoSaveTicket;
use OEModule\PatientTicketing\models;

class TicketAssignIsPatientCalled extends BaseTicketAssignment
{
    public $form_data;
    public $is_patient_called = null;

    public function getAutoSaveData()
    {
        return AutoSaveTicket::getFormData($this->ticket->patient_id, $this->ticket->current_queue->id);
    }

    public function attributeLabels()
    {
        return models\TicketQueueAssignment::model()->attributeLabels();
    }

    public function getAttributeLabel($label)
    {
        return $this->attributeLabels()[$label];
    }

    public function validate($form_data)
    {
        $errs = array();
        if(!array_key_exists('is_patient_called',$form_data)){
            $errs[] = "'Did you telephone the patient during this review' must be selected.";
        }
        return $errs;
    }

    public function isPatientCalledFormFields(
        $htmlOptions = array()
    ) {
        if (!is_null($this->form_data) && isset($this->form_data['patientticketing_glreview']['is_patient_called'])) {
            $selected_item = $this->form_data['patientticketing_glreview']['is_patient_called'];
        } else {
            $json_data = json_decode($this->ticket->current_queue_assignment->details, true);
            $selected_item = @$json_data['is_patient_called']?$json_data['is_patient_called'] : null;
        }

        $this->widget('application.widgets.RadioButtonList', array(
            'element' => $this,
            'name' => 'patientticketing_glreview[is_patient_called]',
            'field' => 'is_patient_called',
            'data' => [0 => 'No', 1 => 'Yes'],
            'selected_item' => $selected_item,
            'htmlOptions' => $htmlOptions,
        ));
    }
}
