<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PatientTicketing\widgets;

use OEModule\PatientTicketing\components\AutoSaveTicket;
use OEModule\PatientTicketing\models;

class TicketAssignOutcome extends BaseTicketAssignment
{
    public $hideFollowUp = true;
    public $form_data;

    public function run()
    {
        if (isset($this->form_data[$this->form_name])) {
            if ($outcome_id = @$this->form_data[$this->form_name]['outcome']) {
                $outcome = models\TicketAssignOutcomeOption::model()->findByPk((int) $outcome_id);
                if ($outcome->followup) {
                    $this->hideFollowUp = false;
                }
            }
        }

        parent::run();
    }

    public function getOutcomeOptions()
    {
        $res = array('options' => array());
        $models = models\TicketAssignOutcomeOption::model()->findAll();
        foreach ($models as $opt) {
            $res['options'][(string) $opt->id] = array('data-followup' => $opt->followup);
        }
        $res['list_data'] = \CHtml::listData($models, 'id', 'name');

        return $res;
    }

    public function getAutoSaveData()
    {
        return AutoSaveTicket::getFormData($this->ticket->patient_id, $this->ticket->current_queue->id);
    }

    /**
     * Extract form data for storing in assignment table.
     *
     * @param $form_data
     *
     * @return array|void
     */
    public function extractFormData($form_data)
    {
        $res = array();
        foreach (array('outcome', 'followup_quantity', 'followup_period', 'clinic_location') as $k) {
            $res[$k] = @$form_data[$k];
        }

        return $res;
    }

    /**
     * Perform form data validation.
     *
     * @param $form_data
     *
     * @return array
     */
    public function validate($form_data)
    {
        $errs = array();
        if (!@$form_data['outcome']) {
            $errs['outcome'] = 'Please select an outcome';
        }

        $outcome = models\TicketAssignOutcomeOption::model()->findByPk((int) $form_data['outcome']);
        if ($outcome && $outcome->followup) {
            // validate outcome fields
            foreach (array(
                 'followup_quantity' => 'follow up quantity',
                 'followup_period' => 'follow up period',
                 'clinic_location' => 'clinic location', ) as $k => $v) {
                if (!@$form_data[$k]) {
                    $errs[$k] = "Please select {$v}";
                }
            }
        }

        return $errs;
    }

    /**
     * Stringify the provided data structure for this widget.
     *
     * @param $data
     *
     * @return string
     */
    public function formatData($data)
    {
        $res = $data['outcome'];
        if (@$data['followup_quantity']) {
            $res .= ' in '.$data['followup_quantity'].' '.$data['followup_period'].' at '.$data['clinic_location'];
        }

        return $res;
    }

    /**
     * Set episode status for relevant choices in the outcome field.
     *
     * @param $ticket
     * @param $data
     *
     * @throws \Exception
     */
    public function processAssignmentData($ticket, $data)
    {
        if (!$outcome_id = $data['outcome']) {
            throw new \Exception('Invalid data for processing - outcome is required field');
        }
        if (!$outcome = models\TicketAssignOutcomeOption::model()->findByPk((int) $outcome_id)) {
            throw new \Exception("Cannot find outcome with id {$outcome_id}");
        }
        if ($episode_status = $outcome->episode_status) {
            $t_svc = \Yii::app()->service->getService('PatientTicketing_Ticket');
            $ep = $t_svc->getTicketEpisode($ticket);
            $ep->episode_status_id = $episode_status->id;
            $ep->save();
        }
    }

    /**
     * Generate string from the widget captured data.
     *
     * @param $data
     *
     * @return string|void
     */
    public function getReportString($data)
    {
        $res = '';
        if ($outcome_id = @$data['outcome']) {
            $outcome = models\TicketAssignOutcomeOption::model()->findByPk((int) $outcome_id);
            $res = $outcome->name;
            if ($outcome->followup) {
                if (@$data['followup_quantity'] == 1 && isset($data['followup_period'])) {
                    $data['followup_period'] = rtrim($data['followup_period'], 's');
                }
                $res .= ' in '.@$data['followup_quantity'].' '.@$data['followup_period'];
                $res .= ' at '.@$data['clinic_location'];
            }
        }

        return $res;
    }
}
