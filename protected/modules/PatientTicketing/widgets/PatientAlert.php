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

use OEModule\PatientTicketing\components;
use Yii;

class PatientAlert extends \PatientAlertWidget
{
    public $assetFolder;
    public $shortName;

    public function init()
    {
        // if the widget has javascript, load it in
        $cls_name = explode('\\', get_class($this));
        $this->shortName = array_pop($cls_name);
        if (file_exists(dirname(__FILE__).'/js/'.$this->shortName.'.js')) {
            $this->assetFolder = Yii::app()->getAssetManager()->publish(dirname(__FILE__) . '/js/', true);
        }
        parent::init();
    }

    public function run()
    {
        $t_svc = Yii::app()->service->getService('PatientTicketing_Ticket');

        $tickets = $t_svc->getTicketsForPatient($this->patient);
        $match = false;

        if ($curr_ids = Yii::app()->session['patientticket_ticket_ids']) {
            foreach ($tickets as $ticket) {
                if ($ticket->id == $curr_ids[0]) {
                    $match = true;
                    break;
                }
            }
            if (!$match) {
                // either viewing a different patient, or the ticket has been closed
                Yii::app()->session['patientticket_ticket_ids'] = null;
            }
        } else {
            $curr_ids = array();
        }

        $this->render('PatientAlert', array(
                'tickets' => $tickets,
                't_svc' => $t_svc,
                'summary_widget' => components\PatientTicketing_API::$TICKET_SUMMARY_WIDGET,
                'current_ticket_ids' => $curr_ids,
            ));
    }
}
