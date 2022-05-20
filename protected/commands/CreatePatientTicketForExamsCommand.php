<?php

use OEModule\PatientTicketing\models\QueueSet;
use OEModule\PatientTicketing\services\PatientTicketing_Ticket;

/**
 * (C) OpenEyes Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class CreatePatientTicketForExamsCommand extends CConsoleCommand
{
    public function run($args)
    {
        $errors = array();

        $queueset_id = null;
        $service_subspecialty_id = null;
        $context_id = null;
        $min_age = null;
        $max_age = null;
        $max_days = null;
        $user_id = null;
        $only_most_recent = false;

        foreach ($args as $arg) {
            //Determine if the argument marches the expected form
            if (preg_match("/--[a-z\-]+=[0-9]+/", $arg)) {
                //Split the argument to key and value
                list($arg_key, $arg_value) = explode("=", $arg);

                //Shadow the variable to one that is explicitly cast
                $arg_value = (int)$arg_value;

                //Match the key and set the applicable value
                switch ($arg_key) {
                    case "--user-id":
                        $user_id = $arg_value;
                        break;
                    case "--queueset-id":
                        $queueset_id = $arg_value;
                        break;
                    case "--service-subspecialty-id":
                        $service_subspecialty_id = $arg_value;
                        break;
                    case "--context-id":
                        $context_id = $arg_value;
                        break;
                    case "--min-age":
                        $min_age = $arg_value;
                        break;
                    case "--max-age":
                        $max_age = $arg_value;
                        break;
                    case "--max-days":
                        $max_days = $arg_value;
                        break;
                    case "--only-most-recent-event":
                        $only_most_recent = boolval($arg_value);
                        break;
                    default:
                        $errors[] = "Unrecognised option: " . $arg_key;
                        break;
                }
            } else {
                //Reject the argument if it is not parsable
                $errors[] = "Rejected argument " . $arg . " as arguments must be of the form --arg=value and the value must be an integer";
            }
        }

        //Ensure that the mandatory user_id variable has been assigned
        if (!isset($user_id)) {
            $errors[] = "The --user-id argument is mandatory";
        }

        //Ensure that the mandatory queueset_id variable has been assigned
        if (!isset($queueset_id)) {
            $errors[] = "The --queueset-id argument is mandatory";
        }

        //Ensure that the mandatory context_id variable has been assigned
        if (!isset($context_id)) {
            $errors[] = "The --context-id argument is mandatory";
        }

        //Print errors if they exist, then terminate
        if (count($errors) > 0) {
            echo "Unable to continue, the following errors were encountered:\n";

            foreach ($errors as $error) {
                echo " - " . $error . "\n";
            }
        } else {
            $event_type_id = EventType::model()->findByAttributes(array('name' => 'Examination'))->id;

            $sql = Yii::app()->db->createCommand()
                ->select("ev.id eid, p.id pid")
                ->from("event ev")
                ->join("episode ep", "ep.id = ev.episode_id")
                ->join("patient p", "p.id = ep.patient_id")
                ->where("ev.event_type_id = :event_type_id", array(":event_type_id" => $event_type_id))
                ->andWhere("ev.deleted = 0")
                ->andWhere("(ep.change_tracker != 1 OR ep.change_tracker IS NULL)")
                ->andWhere("NOT EXISTS (
                        SELECT tck.id FROM patientticketing_ticket tck
                        WHERE tck.event_id = ev.id
                    )");

            if (isset($context_id)) {
                $sql->andWhere("(ev.firm_id IS NULL OR ev.firm_id = :context_id)", array(':context_id' => $context_id));
            }

            if (isset($service_subspecialty_id)) {
                $sql->join("firm f", "f.id = ev.firm_id")
                    ->andWhere("f.service_subspecialty_assignment_id = :service_subspecialty_id", array(':service_subspecialty_id' => $service_subspecialty_id));
            }

            if (isset($min_age)) {
                $sql->andWhere("TIMESTAMPDIFF(YEAR, DATE(p.dob), CURDATE()) >= :min_age", array(":min_age" => $min_age));
            }

            if (isset($max_age)) {
                $sql->andWhere("TIMESTAMPDIFF(YEAR, DATE(p.dob), CURDATE()) <= :max_age", array(":max_age" => $max_age));
            }

            if (isset($max_days)) {
                $sql->andWhere("TIMESTAMPDIFF(DAY, DATE(ev.created_date), CURDATE()) <= :max_days", array(":max_days" => $max_days));
            }

            if ($only_most_recent) {
                $sql->andWhere("NOT EXISTS (
                        SELECT ev2.id FROM event ev2
                        JOIN episode ep2 ON ep2.id = ev2.episode_id
                        WHERE ev2.event_type_id = :event_type_id 
                            AND ep2.patient_id = ep.patient_id 
                            AND ev2.created_date > ev.created_date
                    )", array(":event_type_id" => $event_type_id));
            }

            $results = $sql->queryAll();

            $api = new \OEModule\PatientTicketing\components\PatientTicketing_API();

            $queueset = QueueSet::model()->findByPk($queueset_id);
            $queue = $queueset->initial_queue;

            $total_results = count($results);

            if ($total_results === 0) {
                echo "No applicable events found.";
            }

            foreach ($results as $i => $result) {
                $data = array('patientticketing__priority' => null);

                $event = Event::model()->findByPk($result['eid']);
                $context = Firm::model()->findByPk($context_id);
                $display_i = $i + 1;

                if (isset($event)) {
                    $api->createTicketForEvent($event, $queue, $user_id, $context, $data, true);

                    echo "\rCreating ticket ($display_i/$total_results)";
                } else {
                    echo "\rSkipping ticket ($display_i/$total_results) as no event was found\n";
                }
            }

            echo "\n";
        }
    }

    public function actionHelp()
    {
        echo("DESCRIPTION: 
Automatically adds a patient ticket to any examination event that meets a set of criteria, and where no patient ticket currently exists.

USAGE: php yiic createpatientticketforexams --queueset-id=QUEUESET_ID [--service-subspecialty-id=SERVICE_SUBSPECIALTY_ID] [--context-id=CONTEXT_ID] [--min-age=MINIMUM_AGE] [--max-age=MAXIMUM_AGE] [--max-days=MAXIMUM_DAYS]

COMMAND OPTIONS:
    --user-id=USER_ID                                   : Will create models with the id of the user specified by USER_ID
    --queueset-id=QUEUESET_ID                           : Assigns resulting patient tickets to the queueset specified by QUEUESET_ID
    --context-id=CONTEXT_ID                             : Assigns resulting patient tickets to and filters events by the context specified by CONTEXT_ID
    --service-subspecialty-id=SERVICE_SUBSPECIALTY_ID   : Applies command only to examination events with the associated service subspecialty assignment specified by SERVICE_SUBSPECIALTY_ASSIGNMENT_ID
    --min-age=MINIMUM_AGE                               : Applies command only to patients older (in years) than the age specified by MINIMUM_AGE
    --max-age=MAXIMUM_AGE                               : Applies command only to patients older (in years) than the age specified by MAXIMUM_AGE
    --max-days=MAXIMUM_DAYS                             : Applies command only to examination events that are older (in days) then the amount of days specified by MAXIMUM_DAYS
    --only-most-recent-event=ONLY_RECENT                : When ONLY_RECENT is 1, will only create a ticket for the patient's most recent event");
    }
}
