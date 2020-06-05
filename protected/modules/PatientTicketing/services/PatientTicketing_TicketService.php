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

namespace OEModule\PatientTicketing\services;

use OEModule\PatientTicketing\models;
use Yii;

class PatientTicketing_TicketService extends \services\ModelService
{
    protected static $primary_model = 'OEModule\PatientTicketing\models\Ticket';

    /**
     * Pass through wrapper to generate Queue Resource.
     *
     * @param OEModule\PatientTicketing\models\Ticket $ticket
     *
     * @return resource
     */
    public function modelToResource($ticket)
    {
        $res = parent::modelToResource($ticket);
        foreach (array('patient_id', 'priority_id', 'report', 'assignee_user_id', 'assignee_date',
             'created_user_id', 'created_date', 'last_modified_user_id', 'last_modified_date', 'event_id', ) as $pass_thru) {
            $res->$pass_thru = $ticket->$pass_thru;
        }

        return $res;
    }

    /**
     * @param models\Ticket $ticket
     *
     * @return array|mixed|null|string
     */
    public function getTicketActionLabel(models\Ticket $ticket)
    {
        if (!$ticket->is_complete()) {
            if ($label = $ticket->current_queue->action_label) {
                return $label;
            } else {
                return 'Move';
            }
        }
    }

    /**
     * @param \Patient $patient
     * @param bool     $active
     *
     * @return models\Ticket[]
     */
    public function getTicketsForPatient(\Patient $patient, $active = true)
    {
        $criteria = new \CDbCriteria(array('order' => 't.created_date desc'));
        $criteria->addColumnCondition(array('patient_id' => $patient->id));

        $tickets = models\Ticket::model()->with('current_queue')->findAll($criteria);
        if ($active) {
            $res = array();
            foreach ($tickets as $t) {
                if (!$t->current_queue) {
                    continue;
                }

                if (!$t->is_complete()) {
                    $res[] = $t;
                }
            }

            return $res;
        } else {
            return $tickets;
        }
    }

    /**
     * Get the queueset category for the given ticket.
     *
     * @param models\Ticket $ticket
     *
     * @return models\QueueSetCategory
     */
    public function getCategoryForTicket(models\Ticket $ticket)
    {
        $qs_svc = Yii::app()->service->getService('PatientTicketing_QueueSet');
        $qs = $qs_svc->getQueueSetForQueue($ticket->current_queue->id);

        return $qs->category;
    }

    /**
     * Get the episode for the ticket if one exists.
     *
     * @param models\Ticket $ticket
     *
     * @return \Episode|null
     */
    public function getTicketEpisode(models\Ticket $ticket)
    {
        if ($event = $ticket->event) {
            return $event->episode;
        }

        return;
    }
}
