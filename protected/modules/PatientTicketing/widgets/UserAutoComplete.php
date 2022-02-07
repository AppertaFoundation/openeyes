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

use OEModule\PatientTicketing\models\Queue;
use OEModule\PatientTicketing\models\UserWidgetAssignment;

/**
 * Example usage:
 * In patientticketing_queue.report_definition
 *                        ↓↓↓↓
 * <b>Consultant:</b>[pt_consultant]<br />
 * <b>Risk:</b> [glr]<br /> <b>IOP:</b><br />[iot] <br /> [vaf] <br />
 *
 * In patientticketing_queue.assignment_fields
 * [
 *               ↓↓↓↓
 *     {"id":"consultant","type":"widget","widget_name":"UserAutoComplete"},
 *     {"id":"glreview","type":"widget","widget_name":"TicketAssignOutcome"},
 * ]
 */
class UserAutoComplete extends BaseTicketAssignment
{
    public $form_data;
    public $jsPriority = 100; // let OpenEyes.UI.DOM load first
    public ?\User $user = null;

    public function run()
    {
        if (isset($this->assignment_field['assignment_fields']['DefaultToServiceUser']) && $this->assignment_field['assignment_fields']['DefaultToServiceUser'] === 'true') {
            // Defaults to the user account assigned to the service context of the examination event creating the ticket
            $this->user = $this->episode->firm->consultant ?? $this->ticket->event->episode->firm->consultant ?? null;
        }

        $this->loadFromPost();
        $this->render('UserAutoComplete');
    }

    public function getFieldName()
    {
        return "{$this->form_name}_id";
    }

    public function extractFormData($form_data)
    {
        return $form_data; // no modification needed, it is only an id
    }

    public function validate($value)
    {
        $errs = [];

        if (!$value || !is_numeric($value)) {
            $errs["{$this->form_name}_id"] = ($this->label ?: 'User') . ' is required';
        }

        return $errs;
    }

    public function getReportString($user_id)
    {
        if (isset($this->assignment_field['id'])) {
            $assignment = UserWidgetAssignment::model()->findByAttributes([
                'ticket_id' => $this->ticket->id,
                'queue_id' => $this->queue->id,
                'widget_id' => $this->assignment_field['id']
            ]);

            return \User::model()->findByPk($assignment->user_id ?? null)->fullNameAndTitle ?? null;
        }

        return null;
    }

    public function processAssignmentData($ticket, $data)
    {
        $attributes = [
            'ticket_id' => $this->ticket->id,
            'queue_id' => $this->queue->id,
            'widget_id' => $this->assignment_field['id']
        ];

        $assignment = UserWidgetAssignment::model()->findByAttributes($attributes);

        if (!$assignment) {
            $assignment = new UserWidgetAssignment();
            $assignment->attributes = $attributes;
            $assignment->user_id = $data;
        }

        if (!$assignment->save()) {
            \OELog::log("UserAutoComplete widget save failed");
            \OELog::log(print_r($assignment->getErrors()));
        }
    }

    public function loadFromPost()
    {
        $user_id = \Yii::app()->request->getParam("{$this->form_name}_id");
        if ($user_id) {
            $this->user = \User::model()->findByPk($user_id);
        }
    }
}
