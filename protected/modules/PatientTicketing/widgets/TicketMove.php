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
use Yii;

class TicketMove extends \CWidget
{
    public $event_types;
    public $ticket_info;
    public $current_queue_name;
    public $outcome_options;
    public $ticket;

    protected $outcome_queue_id;

    public $assetFolder;
    public $shortName;

    public function run()
    {
        //TODO: genericise this behaviour
        $cls_name = explode('\\', get_class($this));
        $this->shortName = array_pop($cls_name);
        if (file_exists(dirname(__FILE__).'/js/'.$this->shortName.'.js')) {
            $this->assetFolder = Yii::app()->getAssetManager()->publish(dirname(__FILE__) . '/js/', true);
            Yii::app()->getClientScript()->registerScriptFile($this->assetFolder.'/'.$this->shortName.'.js');
        }

        if ($this->ticket) {
            $this->event_types = $this->ticket->current_queue->getRelatedEventTypes();
            $this->ticket_info = $this->ticket->getInfoData(false);
            $this->current_queue_name = $this->ticket->current_queue->name;
            $this->outcome_options = array();
            $od = $this->ticket->current_queue->getOutcomeData(false);
            foreach ($od as $out) {
                $this->outcome_options[$out['id']] = $out['name'];
            };
            if (count($od) == 1) {
                $this->outcome_queue_id = $od[0]['id'];
            } else {
                $form_data = AutoSaveTicket::getFormData($this->ticket->patient_id, $this->ticket->current_queue->id);
                if ($form_data) {
                    $this->outcome_queue_id = $form_data['to_queue_id'];
                }
            }
        }

        $this->render('TicketMove');
    }
}
