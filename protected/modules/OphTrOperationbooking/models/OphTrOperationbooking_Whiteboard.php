<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
class OphTrOperationbooking_Whiteboard extends BaseActiveRecordVersioned
{
    /**
     * Returns the static model of the specified AR class.
     *
     * @return the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophtroperationbooking_whiteboard';
    }

    public function update($id)
    {
        // TODO: The data fetch should be performed here, which
        // should write collated whiteboard data to the Whiteboard model.
        // The fetch() method should just pull from the Whiteboard model.
    }

    public function fetch($id)
    {
        // TODO: Group these pulls in to one join query? Or leave separate for clearer reading
        $booking = Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($id));
        $event = Event::model()->find('id=?', array($id));
        $episode = Episode::model()->find('id=?', array($event->episode_id));
        $patient = Patient::model()->find('id=?', array($episode->patient_id));
        $contact = Contact::model()->find('id=?', array($patient->contact_id));

        $allergies = Yii::app()->db->createCommand()
            ->select('a.name as name')
            ->from('patient_allergy_assignment pas')
            ->leftJoin('allergy a', 'pas.allergy_id = a.id')
            ->where("pas.patient_id = {$episode->patient_id}")
            ->order('a.name')
            ->queryAll();

        $allergyString = '';
        foreach ($allergies as $a) {
            $allergyString .= $a['name'].', ';
        }

        if ($allergyString) {
            $allergyString = substr($allergyString, 0, -2);
        }

        if (!$allergyString) {
            $allergyString = 'None';
        }

        $operation = Yii::app()->db->createCommand()
            ->select('proc.term as term')
            ->from('et_ophtroperationbooking_operation op')
            ->leftJoin('ophtroperationbooking_operation_procedures_procedures opp', 'opp.element_id = op.id')
            ->leftJoin('proc', 'opp.proc_id = proc.id')
            ->where("op.event_id = {$id}")
            ->queryAll();

        $eyes = array(1 => 'Left', 2 => 'Right', 3 => 'Both');    // TODO: pull from DB/Join?

        $data['eye_id'] = $booking->eye_id;
        $data['eyeSide'] = $eyes[$data['eye_id']];

        $data['predictedAdditionalEquipment'] = $booking->special_equipment_details;
        $data['comments'] = $booking->comments."\n".$booking->comments_rtt;

        $data['patientName'] = $contact['title'].' '.$contact['first_name'].' '.$contact['last_name'];
        $data['dob'] = date('j M Y', strtotime($patient['dob']));
        $data['hos_num'] = $patient['hos_num'];
        $data['procedure'] = $operation[0]['term'];
        $data['allergies'] = $allergyString;
        $data['iol_model'] = 'unknown';    // TODO
        $data['iol_power'] = 'none';        // TODO
        $data['predictedRefractiveOutcome'] = '-0.0 D';    // TODO
        $data['alphaBlockers'] = 'N/A';    // TODO
        $data['anticoagulants'] = 'Anti-N/A';    // TODO
        $data['inr'] = 'None';    // TODO

        return $data;
    }
}
