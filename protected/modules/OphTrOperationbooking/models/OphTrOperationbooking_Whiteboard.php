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
     * @return OphTrOperationbooking_Whiteboard the static model class
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
        $booking = Element_OphTrOperationbooking_Operation::model()->find('event_id=?', array($id));

        $eyes = CHtml::listData(Eye::model()->findAll(), 'id', 'name');
        if($eyes[$booking->eye_id] === 'Both'){
            throw new CHttpException(400, 'Can\'t display whiteboard for dual eye bookings');
        }
        $eyeLabel = strtolower($eyes[$booking->eye_id]);

        $event = Event::model()->findByPk($id);
        $episode = Episode::model()->findByPk($event->episode_id);
        $patient = Patient::model()->findByPk($episode->patient_id);
        $contact = Contact::model()->findByPk($patient->contact_id);

        $biometryCriteria = new CDbCriteria();
        $biometryCriteria->addCondition('patient_id = :patient_id');
        $biometryCriteria->params = array('patient_id' => $patient->id);
        $biometryCriteria->order = 'last_modified_date DESC';
        $biometryCriteria->limit = 1;
        $biometry = Element_OphTrOperationnote_Biometry::model()->find($biometryCriteria);

        $examination = $event->getPreviousInEpisode(EventType::model()->findByAttributes(array('name' => 'Examination'))->id);

        $allergies = Yii::app()->db->createCommand()
            ->select('a.name as name')
            ->from('patient_allergy_assignment pas')
            ->leftJoin('allergy a', 'pas.allergy_id = a.id')
            ->where("pas.patient_id = {$episode->patient_id}")
            ->order('a.name')
            ->queryAll();

        $allergyString = 'None';
        if($allergies){
            $allergyString = implode(',', array_column($allergies, 'name'));
        }

        $operation = Yii::app()->db->createCommand()
            ->select('proc.term as term')
            ->from('et_ophtroperationbooking_operation op')
            ->leftJoin('ophtroperationbooking_operation_procedures_procedures opp', 'opp.element_id = op.id')
            ->leftJoin('proc', 'opp.proc_id = proc.id')
            ->where("op.event_id = {$id}")
            ->queryAll();


        $data['eye_id'] = $booking->eye_id;
        $data['eye_side'] = $eyes[$data['eye_id']];
        $data['predicted_additional_equipment'] = $booking->special_equipment_details;
        $data['comments'] = $booking->comments."\n".$booking->comments_rtt;
        $data['patient_name'] = $contact['title'].' '.$contact['first_name'].' '.$contact['last_name'];
        $data['dob'] = date('j M Y', strtotime($patient['dob']));
        $data['hos_num'] = $patient['hos_num'];
        $data['procedure'] = implode(',', array_column($operation, 'term'));
        $data['allergies'] = $allergyString;
        $data['iol_model'] = ($biometry) ? $biometry->attributes['lens_description_'.$eyeLabel] : 'unknown';
        $data['iol_power'] = ($biometry) ? $biometry->attributes['iol_power_'.$eyeLabel] : 'none';
        $data['predicted_refractive_outcome'] = '-0.0 D';
        $data['alpha_blockers'] = $patient->hasRisk('Alpha blockers');
        $data['anticoagulants'] = $patient->hasRisk('Anticoagulants');
        $data['inr'] = 'None';

        return $data;
    }
}
