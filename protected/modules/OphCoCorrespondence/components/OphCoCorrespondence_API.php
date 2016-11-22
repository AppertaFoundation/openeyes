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
class OphCoCorrespondence_API extends BaseAPI
{
    /**
     * @param int $event_id
     *
     * @return bool
     */
    public function canUpdate($event_id)
    {
        $letter = ElementLetter::model()->find('event_id=?', array($event_id));

        // for the new correspondence with DocMan
        // once the letter is generated for the DocMan only admin can edit
        return !$letter->isGeneratedForDocMan();

        // FIXME: Correspondence locking is suspended while draft usage is discussed
        return true;

        return $letter->isEditable();
    }

    public function getLatestEvent($episode)
    {
        $event_type = $this->getEventType();

        if ($event = $episode->getMostRecentEventByType($event_type->id)) {
            return $event;
        }
    }

    public function getMacroTargets($patient_id, $macro_id)
    {
        if (!$patient = Patient::model()->findByPk($patient_id)) {
            throw new Exception('Patient not found: '.$patient_id);
        }

        if (!$macro = LetterMacro::model()->findByPk($macro_id)) {
            throw new Exception('Macro not found: '.$macro_id);
        }

        $data = array();
        if ($macro->recipient && $macro->recipient->name == 'Patient') {
            $contact = $patient;
            $data['to']['contact_type'] = get_class($contact);
            $data['to']['contact_id'] = $contact->contact->id;
            if ($patient->date_of_death) {
                echo json_encode(array('error'=>'DECEASED'));
                return;
            }
        }

        if ($macro->recipient && $macro->recipient->name == 'GP' && $contact = ($patient->gp) ? $patient->gp : $patient->practice) {
            $data['to']['contact_type'] = get_class($contact);
            $data['to']['contact_id'] = $contact->contact->id;
        }

        if (isset($contact)) {
            $data['to']['contact_name'] = $contact->getFullName();
            $data['to']['address'] = $contact->getLetterAddress(array(
                                    'patient' => $patient,
                                    'include_name' => false,
                                    'include_label' => false,
                                    'delimiter' => "\n",
                                ));


            if (! $data['to']['address']){
                $data['to']['address'] = "The contact does not have a valid address.";
            }
        }

        $data['use_nickname'] = $macro->use_nickname;

        $k = 0;
        if ($macro->cc_patient) {
            $data['cc'][$k]['contact_type'] = 'Patient';
            if ($patient->date_of_death) {
                $data['cc'][$k]['contact_name'] = "Warning: the patient cannot be cc'd because they are deceased.";
                $data['cc'][$k]['address'] = null;
            } elseif ($patient->contact->address) {
                $data['cc'][$k]['contact_name'] = $patient->getFullName();
                $data['cc'][$k]['contact_id'] = $patient->contact->id;
                $data['cc'][$k]['address'] = $patient->getLetterAddress(array(
                            'include_name' => false,
                            'include_label' => false,
                            'delimiter' => "\n",
                            'include_prefix' => false,
                        ));
            } else {
                $data['cc'][$k]['contact_name'] = $patient->getFullName();
                $data['cc'][$k]['contact_id'] = $patient->contact->id;
                $data['cc'][$k]['address'] = "Letters to the GP should be cc'd to the patient, but this patient does not have a valid address.";
            }
            $k++;
        }

        if ($macro->cc_doctor && $cc_contact = ($patient->gp) ? $patient->gp : $patient->practice) {
            $data['cc'][$k]['contact_type'] = 'GP';
            $data['cc'][$k]['contact_name'] = $cc_contact->getFullName();
            $data['cc'][$k]['contact_id'] = $cc_contact->contact->id;
            $data['cc'][$k]['address'] = $cc_contact->getLetterAddress(array(
                    'patient' => $patient,
                    'include_name' => false,
                    'include_label' => false,
                    'delimiter' => "\n",
                    'include_prefix' => false,
                ));
            $k++;
        }

        if ($macro->cc_drss) {
            $commissioningbodytype = CommissioningBodyType::model()->find('shortname = ?', array('CCG'));
            $commissioningbody = $patient->getCommissioningBodyOfType($commissioningbodytype);
            if($commissioningbodytype && $commissioningbody) {
                foreach($commissioningbody->services as $service) {
                    if($service->type->shortname == 'DRSS') {
                        $data['cc'][$k]['contact_type'] = 'DRSS';
                        $data['cc'][$k]['contact_name'] = $service->getFullName();
                        $data['cc'][$k]['contact_id'] = $service->contact->id;
                        $data['cc'][$k]['address'] = $service->getLetterAddress(array(
                                        'include_name' => false,
                                        'include_label' => false,
                                        'delimiter' => "\n",
                                        'include_prefix' => false,
                                    ));

                        break;
                    }
                }
            }
        }

        $data['macro_id'] = $macro_id;

        return $data;
    }

    private function getMacroData($patient_id, $macro_id)
    {
        if(!$patient_id){
            $patient_id = @$_GET['patient_id'];
        }
        if(!$macro_id){
            $macro_id = @$_GET['macro_id'];
        }

        if (!$patient = Patient::model()->findByPk($patient_id)) {
            throw new Exception('Patient not found: '.$patient_id);
        }

        if (!$macro = LetterMacro::model()->findByPk($macro_id)) {
            throw new Exception('Macro not found: '.$macro_id);
        }

        $data = array();

        $macro->substitute($patient);

        if ($macro->recipient && $macro->recipient->name == 'Patient') {
            $contact = $patient;
            if ($patient->date_of_death) {
                echo json_encode(array('error'=>'DECEASED'));
                return;
            }
        }

        if ($macro->recipient && $macro->recipient->name == 'GP' && $contact = ($patient->gp) ? $patient->gp : $patient->practice) {
            $data['sel_address_target'] = get_class($contact).$contact->id;
        }

        if (isset($contact)) {
            $address = $contact->getLetterAddress(array(
                'patient' => $patient,
                'include_name' => true,
                'include_label' => true,
                'delimiter' => "\n",
            ));

            if($address){
                $data['address'] = $address;
            }
            else {
                $data['alert'] = "The contact does not have a valid address.";
                $data['address'] = 'No valid address!';
            }

            $data['introduction'] = $contact->getLetterIntroduction(array(
                'nickname' => $macro->use_nickname,
            ));
        }

        $data['use_nickname'] = $macro->use_nickname;

        if ($macro->body) {
            $data['body'] = $macro->body;
        }

        $cc = array(
            'text' => array(),
            'targets' => array()
        );
        if ($macro->cc_patient) {
            if ($patient->date_of_death) {
                $data['alert'] = "Warning: the patient cannot be cc'd because they are deceased.";
            } elseif ($patient->contact->address) {
                $cc['text'][] = $patient->getLetterAddress(array(
                    'include_name' => true,
                    'include_label' => true,
                    'delimiter' => ", ",
                    'include_prefix' => true,
                ));
            } else {
                $data['alert'] = "Letters to the GP should be cc'd to the patient, but this patient does not have a valid address.";
            }
        }

        if ($macro->cc_doctor && $cc_contact = ($patient->gp) ? $patient->gp : $patient->practice) {
            $cc['text'][] = $cc_contact->getLetterAddress(array(
                'patient' => $patient,
                'include_name' => true,
                'include_label' => true,
                'delimiter' => ", ",
                'include_prefix' => true,
            ));
        }

        if ($macro->cc_drss) {
            $commissioningbodytype = CommissioningBodyType::model()->find('shortname = ?', array('CCG'));
            if($commissioningbodytype && $commissioningbody = $patient->getCommissioningBodyOfType($commissioningbodytype)) {
                $drss = null;
                foreach($commissioningbody->services as $service) {
                    if($service->type->shortname == 'DRSS') {
                        $cc['text'][] = $service->getLetterAddress(array(
                            'include_name' => true,
                            'include_label' => true,
                            'delimiter' => ", ",
                            'include_prefix' => true,
                        ));
                        break;
                    }
                }
            }
        }

        $data['cc'] = implode("\n",$cc['text']);
        $data['date'] = date('Y-m-d');
        $data['site_id'] = Yii::app()->session['selected_site_id'];
        $data['footer'] = "Yours sincerely\n\n\n\n\n".User::model()->findByPk(Yii::app()->user->id)->fullName."\n".User::model()->findByPk(Yii::app()->user->id)->role."\n";
        //.(ui.item.consultant?"Consultant: "+ui.item.consultant:'')
        return $data;
    }

    /***
     * Creates the new letter
     *
     * @param $event
     * @param $macro_id
     */
    public function createCorrespondenceContent($event, $macro_id){
        $letterContent = $this->getMacroData($event->episode->patient_id, $macro_id);
        $correspondenceData = new ElementLetter();
        $correspondenceData->setAttributes($letterContent);
        $correspondenceData->event_id = $event->id;
        $correspondenceData->save();
    }

    /***
     * Creates a new correspondence event in the specified episode
     *
     * @param $episode_id
     */
    public function createNewCorrespondenceEvent($episode_id){
        $event = new Event();
        $event->episode_id = $episode_id;
        $event_type = EventType::model()->find('class_name=:class_name', array(':class_name'=>'OphCoCorrespondence'));
        $event->event_type_id = $event_type->id;
        $event->event_date = date('Y-m-d');
        $event->save();
        return $event;
    }
    
    /**
     * Returns the letter targets by element id
     * 
     * @param int $id
     * @return array
     */
    public function getMacroTargetsByElementLetterId($id)
    {
        $element_letter = ElementLetter::model()->findByPk($id);
        return $element_letter->letter_targets;
    }
}
