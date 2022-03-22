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

use OEModule\PatientTicketing\models;
use OEModule\PatientTicketing\components\AutoSaveTicket;
use Yii;

/**
 * Class QueueAssign.
 *
 * Widget to generate the assignment form for a Queue
 */
class QueueAssign extends \CWidget
{
    public $ticket;
    public $queue_id;
    public $current_queue_id;
    public $label_width = 4;
    public $data_width = 8;
    public $queue_select_label = 'Queue';
    public $patient_id;
    public $assetFolder;
    public $shortName;
    public $extra_view_data = array();
    public $episode_id = null;
    public $is_template = false;

    public function run()
    {
        $cls_name = explode('\\', get_class($this));
        $this->shortName = array_pop($cls_name);
        if (file_exists(dirname(__FILE__) . '/js/' . $this->shortName . '.js')) {
            $this->assetFolder = Yii::app()->getAssetManager()->publish(dirname(__FILE__) . '/js/', true);
            Yii::app()->getClientScript()->registerScriptFile($this->assetFolder . '/' . $this->shortName . '.js');
        }

        if ($this->queue_id) {
            $queue = models\Queue::model()->findByPk($this->queue_id);
        } else {
            $queue = null;
        }

        $form_fields = $queue->getFormFields();
        $auto_save = false;
        if (isset($_POST[$form_fields[0]['form_name']])) { // if post contains patient ticket data
            $form_data = $_POST;
        } elseif ($form_data = AutoSaveTicket::getFormData($this->patient_id, $this->current_queue_id)) {
            $auto_save = true;
        }

        //if this is the outcome widget and a correspondence has been created
        //display the print letter button
        $print_letter_event = false;
        foreach ($form_fields as $fld) {
            if (@$fld['widget_name'] == 'TicketAssignAppointment') {
                if ($api = \Yii::app()->moduleAPI->get('OphCoCorrespondence')) {
                    if ($episode = $this->ticket->patient->getEpisodeForCurrentSubspecialty()) {
                        if ($event = $api->getLatestEventInEpisode($episode)) {
                            if ($event->created_date > $this->ticket->created_date) {
                                $print_letter_event = $event;
                            }
                        }
                    }
                }
            }
        }

        $this->render('QueueAssign', array(
            'queue' => $queue,
            'form_fields' => $form_fields,
            'form_data' => $form_data,
            'auto_save' => $auto_save,
            'print_letter_event' => $print_letter_event,
            'extra_view_data' => $this->extra_view_data,
            'episode_id' => $this->episode_id,
        ));
    }
}
