<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphCoCorrespondence_API extends BaseAPI
{
    public const ESIGN_PLACEHOLDER = "{electronic_signature}";

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
        return !$letter->isGeneratedFor(['Docman', 'Internalreferral']);

        // FIXME: Correspondence locking is suspended while draft usage is discussed
        return true;

        return $letter->isEditable();
    }

    public function showDeleteIcon($event_id)
    {
        $letter = ElementLetter::model()->find('event_id=?', array($event_id));

        return !$letter->isGeneratedFor(['Docman', 'Internalreferral']);
    }

    public function getLatestEventInEpisode($episode)
    {
        $event_type = $this->getEventType();

        if ($event = $episode->getMostRecentEventByType($event_type->id)) {
            return $event;
        }
    }

    /**
     * get the full name of the patient for use in correspondence.
     *
     * @param Patient $patient
     *
     * @return string
     */
    public function getFullName($patient)
    {
        $fullname = trim(implode(' ', array($patient->title, $patient->first_name, $patient->last_name)));
        return $fullname;
    }

    /**
     * get the patient title for use in correspondence.
     *
     * @param Patient $patient
     *
     * @return string
     */
    public function getPatientTitle($patient)
    {
        return $patient->title;
    }

    /**
     * get the patient first name for use in correspondence.
     *
     * @param Patient $patient
     *
     * @return string
     */
    public function getFirstName($patient)
    {
        return $patient->first_name;
    }


    /**
     * get the patient last name for use in correspondence.
     *
     * @param Patient $patient
     *
     * @return string
     */
    public function getLastName($patient)
    {
        return $patient->last_name;
    }

    /*
     * get the last Examination Date for patient for use in correspondence.
     *
     * @param Patient $patient
     * @param boolean $use_context
     * @return string
     */

    public function getLastExaminationDate(\Patient $patient, $use_context = false)
    {
        $api = $this->yii->moduleAPI->get('OphCiExamination');
        $event = $api->getLatestVisibleEvent($patient, $use_context);
        if (isset($event->event_date)) {
            return Helper::convertDate2NHS($event->event_date);
        }
        return '';
    }


    /*
     * List of Ophthalmic Diagnoses
     */
    public function getOphthalmicDiagnoses(\Patient $patient)
    {
        return $patient->getUniqueOphthalmicDiagnosesTable();
    }

    /*
     * IOL type from last cataract Operation Note
     * @param $patient
     * @param $use_context
     * @return string
     */
    public function getLastIOLType(\Patient $patient, $use_context = false)
    {
        $name = null;
        $api = $this->yii->moduleAPI->get('OphTrOperationnote');
        $element = $api->getLatestElement('Element_OphTrOperationnote_Cataract', $patient, $use_context);
        if ($element) {
            $name = $element->iol_type ? $element->iol_type->display_name : null;
        }
        return $name;
    }


    /*
     * IOL Power from last cataract operation note
     * @param $patient
     * @param $use_context
     * @return string
     */
    public function getLastIOLPower(\Patient $patient, $use_context = false)
    {
        $api = $this->yii->moduleAPI->get('OphTrOperationnote');
        $element = $api->getLatestElement('Element_OphTrOperationnote_Cataract', $patient, $use_context);
        if ($element) {
            return $element->iol_power;
        }
    }

    /**
     * Get the Latest Refraction - both eyes.
     *
     * @param $patient
     * @param $side - The side is used to define the refraction to be extracted.
     * @param $use_context
     * @return string|null
     */
    public function getLastRefraction(\Patient $patient, $side, $use_context = false)
    {
        $api = $this->yii->moduleAPI->get('OphCiExamination');
        $element = $api->getLatestElement('models\Element_OphCiExamination_Refraction', $patient, $use_context);
        if ($element) {
            return Yii::app()->format->text($element->getPriorityReadingCombined($side));
        }
        return null;
    }

    /**
     * Get the Latest Refraction Date - both eyes.
     *
     * @param $patient
     * @param $use_context
     * @return string|null
     */
    public function getLastRefractionDate(\Patient $patient, $use_context = false)
    {
        $api = $this->yii->moduleAPI->get('OphCiExamination');
        $element = $api->getLatestElement('models\Element_OphCiExamination_Refraction', $patient, $use_context);
        if ($element) {
            return $element->event->event_date;
        }
        return null;
    }

    /*
     * Operated Eye (left/right) from last operation note
     * @param $patient
     * @param $use_context
     * @return string
     */
    public function getLastOperatedEye(\Patient $patient, $use_context = false)
    {
        $api = $this->yii->moduleAPI->get('OphTrOperationnote');
        $element = $api->getLatestElement('Element_OphTrOperationnote_ProcedureList', $patient, $use_context);
        if ($element) {
            return $element->eye->adjective;
        }
    }

    /**
     * Internal abstraction of getting data from before the most recent op note.
     *
     * @param $patient
     * @param bool $use_context
     * @param $api
     * @param $method
     * @return string|null
     */
    private function getPreOpValuesFromAPIMethod($patient, $use_context = false, $api, $method)
    {
        $note_api = $this->yii->moduleAPI->get('OphTrOperationnote');
        if (!$note_api) {
            return null;
        }

        $op_event = $note_api->getLatestEvent($patient, $use_context);
        if ($op_event) {
            $op_event_combined_date = Helper::combineMySQLDateAndDateTime($op_event->event_date, $op_event->created_date);
            $events = $api->getEvents($patient, $use_context, $op_event->event_date);

            foreach ($events as $event) {
                // take account of event date not containing time so we ensure we get the
                // exam from BEFORE the op note, not on the same day but after.

                if ($event->event_date == $op_event->event_date) {
                    if (Helper::combineMySQLDateAndDateTime($event->event_date, $event->created_date) > $op_event_combined_date) {
                        continue;
                    }
                }
                $result = $api->$method($event);
                if ($result) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * Internal abstraction of getting data from before the most recent op note.
     *
     * @param $patient
     * @param bool $use_context
     * @param $api
     * @param $method
     * @return date|null
     */
    private function getPreOpDateFromAPIMethod($patient, $use_context = false, $api, $method)
    {
        if (!$note_api = $this->yii->moduleAPI->get('OphTrOperationnote')) {
            return null;
        }

        $op_event = $note_api->getLatestEvent($patient, $use_context);
        if ($op_event) {
            $op_event_combined_date = Helper::combineMySQLDateAndDateTime($op_event->event_date, $op_event->created_date);
            $events = $api->getEvents($patient, $use_context, $op_event->event_date);

            foreach ($events as $event) {
                // take account of event date not containing time so we ensure we get the
                // exam from BEFORE the op note, not on the same day but after.
                if ($event->event_date == $op_event->event_date) {
                    if (Helper::combineMySQLDateAndDateTime($event->event_date, $event->created_date) > $op_event_combined_date) {
                        continue;
                    }
                }
                if ($result = $api->$method($event)) {
                    return $event->event_date;
                }
            }
        }

        return null;
    }


    /**
     * Get the Pre-Op Visual Acuity - both eyes.
     *
     * @param $patient
     * @param $use_context
     * @return string|null
     */
    public function getPreOpVABothEyes($patient, $use_context = false)
    {
        if ($api = $this->yii->moduleAPI->get('OphCiExamination')) {
            return $this->getPreOpValuesFromAPIMethod($patient, $use_context, $api, 'getBestVisualAcuityFromEvent');
        }
    }

    /**
     * Get the Pre-Op Refraction - both eyes.
     *
     * @param $patient
     * @param $use_context
     * @return string|null
     */
    public function getPreOpRefraction($patient, $use_context = false)
    {
        if ($api = $this->yii->moduleAPI->get('OphCiExamination')) {
            return $this->getPreOpValuesFromAPIMethod($patient, $use_context, $api, 'getRefractionTextFromEvent');
        }
    }

    /**
     * Get Allergies in a bullet format.
     *
     * @param $patient
     *
     * @return string|null
     */
    public function getAllergiesBulleted($patient)
    {
        return $patient->getAllergiesSeparatedString(" - ", "\r\n", true);
    }

    /*
     * @param $patient_id
     * @param $macro_id
     */
    public function getMacroTargets($patient_id, $macro_id)
    {
        if (!$patient = Patient::model()->findByPk($patient_id)) {
            throw new Exception('Patient not found: ' . $patient_id);
        }

        if (!$macro = LetterMacro::model()->findByPk($macro_id)) {
            throw new Exception('Macro not found: ' . $macro_id);
        }

        $data = array();
        if ($macro->recipient && $macro->recipient->name == 'Patient') {
            $contact = $patient;
            $data['to']['contact_type'] = get_class($contact);
            $data['to']['contact_id'] = $contact->contact->id;
            if ($patient->date_of_death) {
                echo json_encode(array('error' => 'DECEASED'));
                return;
            }
            $data['to']['email'] = $contact->contact->email ?? null;
        }

        if ($macro->recipient && $macro->recipient->name == \SettingMetadata::model()->getSetting('gp_label') && $contact = ($patient->gp) ? $patient->gp : $patient->practice) {
            $data['to']['contact_type'] = get_class($contact);
            $data['to']['contact_id'] = $contact->contact->id;
            $data['to']['email'] = $contact->contact->email ?? null;
        }

        if ($macro->recipient && $macro->recipient->name == 'Optometrist') {
            $contact = $contact = $patient->getPatientOptometrist();
            if (isset($contact)) {
                $data['to']['contact_type'] = "Optometrist";
                $data['to']['contact_id'] = $contact->id;
                $data['to']['email'] = $contact->email ?? null;
            }
        }

        if (isset($contact)) {
            $data['to']['contact_name'] = method_exists($contact, "getCorrespondenceName") ? $contact->getCorrespondenceName() : $contact->getFullName();
            $data['to']['contact_nickname'] = $this->getNickname(isset($contact->contact) ? $contact->contact->id : $contact->id);
            $data['to']['address'] = $contact->getLetterAddress(array(
                'patient' => $patient,
                'include_name' => false,
                'include_label' => false,
                'delimiter' => "\n",
            ));


            if (!$data['to']['address']) {
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
                $data['cc'][$k]['contact_name'] = $patient->getCorrespondenceName();
                $data['cc'][$k]['contact_id'] = $patient->contact->id;
                $data['cc'][$k]['address'] = $patient->getLetterAddress(array(
                    'include_name' => false,
                    'include_label' => false,
                    'delimiter' => "\n",
                    'include_prefix' => false,
                ));
            } else {
                $data['cc'][$k]['contact_name'] = $patient->getCorrespondenceName();
                $data['cc'][$k]['contact_id'] = $patient->contact->id;
                $data['cc'][$k]['address'] = "Letters to the " . \SettingMetadata::model()->getSetting('gp_label') . " should be cc'd to the patient, but this patient does not have a valid address.";
            }
            $data['cc'][$k]['email'] = isset($patient->contact) ? $patient->contact->email : null;
            $k++;
        }

        if ($macro->cc_doctor && $cc_contact = ($patient->gp) ? $patient->gp : $patient->practice) {
            $data['cc'][$k]['contact_type'] = \SettingMetadata::model()->getSetting('gp_label');
            $data['cc'][$k]['contact_name'] = $cc_contact->getCorrespondenceName();
            $data['cc'][$k]['contact_id'] = $cc_contact->contact->id;
            $data['cc'][$k]['address'] = $cc_contact->getLetterAddress(array(
                'patient' => $patient,
                'include_name' => false,
                'include_label' => false,
                'delimiter' => "\n",
                'include_prefix' => false,
            ));
            $data['cc'][$k]['email'] = isset($cc_contact->contact) ? $cc_contact->contact->email : null;
            $k++;
        }

        if ($macro->cc_optometrist) {
            $cc_contact = $contact = $patient->getPatientOptometrist();
            if ($cc_contact) {
                $data['cc'][$k]['contact_type'] = "Optometrist";
                $data['cc'][$k]['contact_name'] = $cc_contact->getCorrespondenceName();
                $data['cc'][$k]['contact_id'] = $cc_contact->id;
                $data['cc'][$k]['address'] = $cc_contact->getLetterAddress(array(
                    'patient' => $patient,
                    'include_name' => false,
                    'include_label' => false,
                    'delimiter' => "\n",
                    'include_prefix' => false,
                ));
                $data['cc'][$k]['email'] = isset($cc_contact) ? $cc_contact->email : null;
                $k++;
            }
        }

        if ($macro->cc_drss) {
            $commissioningbodytype = CommissioningBodyType::model()->find('shortname = ?', array('CCG'));
            $commissioningbody = isset($patient->practice) ? $patient->practice->getCommissioningBodyOfType($commissioningbodytype) : null;
            if ($commissioningbodytype && $commissioningbody) {
                foreach ($commissioningbody->services as $service) {
                    if ($service->type->shortname == 'DRSS') {
                        $correspondence_name = $service->fullName;
                        if (method_exists($service, 'getCorrespondenceName')) {
                            $correspondence_name = $service->correspondenceName;
                        }

                        $data['cc'][$k]['contact_type'] = 'DRSS';
                        $data['cc'][$k]['contact_name'] = implode(',', $correspondence_name);
                        $data['cc'][$k]['contact_id'] = $service->contact->id;
                        $data['cc'][$k]['address'] = $service->getLetterAddress(array(
                            'include_name' => false,
                            'include_label' => false,
                            'delimiter' => "\n",
                            'include_prefix' => false,
                        ));
                        $data['cc'][$k]['email'] = isset($service->contact) ? $service->contact->email : null;
                        break;
                    }
                }
            }
        }

        $data['macro_id'] = $macro_id;

        return $data;
    }

    /*
     * @param $patient_id
     * @param $macro_id
     */
    private function getMacroData($patient_id, $macro_id)
    {
        if (!$patient_id) {
            $patient_id = @$_GET['patient_id'];
        }
        if (!$macro_id) {
            $macro_id = @$_GET['macro_id'];
        }

        if (!$patient = Patient::model()->findByPk($patient_id)) {
            throw new Exception('Patient not found: ' . $patient_id);
        }

        if (!$macro = LetterMacro::model()->findByPk($macro_id)) {
            throw new Exception('Macro not found: ' . $macro_id);
        }

        $data = array();

        $macro->substitute($patient);

        if ($macro->recipient && $macro->recipient->name == 'Patient') {
            $contact = $patient;
            if ($patient->date_of_death) {
                echo json_encode(array('error' => 'DECEASED'));
                return;
            }
        }

        if ($macro->recipient && $macro->recipient->name == \SettingMetadata::model()->getSetting('gp_label') && $contact = ($patient->gp) ? $patient->gp : $patient->practice) {
            $data['sel_address_target'] = get_class($contact) . $contact->id;
        }

        if (isset($contact)) {
            $address = $contact->getLetterAddress(array(
                'patient' => $patient,
                'include_name' => true,
                'include_label' => true,
                'delimiter' => "\n",
            ));

            if ($address) {
                $data['address'] = $address;
            } else {
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
                $data['alert'] = "Letters to the " . \SettingMetadata::model()->getSetting('gp_label') . " should be cc'd to the patient, but this patient does not have a valid address.";
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
            if ($commissioningbodytype && $commissioningbody = isset($patient->practice) ? $patient->practice->getCommissioningBodyOfType($commissioningbodytype) : null) {
                $drss = null;
                foreach ($commissioningbody->services as $service) {
                    if ($service->type->shortname == 'DRSS') {
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

        $data['cc'] = implode("\n", $cc['text']);
        $data['date'] = date('Y-m-d');
        $data['site_id'] = Yii::app()->session['selected_site_id'];
        $empty_lines = "\n";
        $meta_data = OphCoCorrespondenceLetterSettingValue::model()->find('`key`=?', array('letter_footer_blank_line_count'));
        $count = $meta_data ? $meta_data->value : 4;
        for ($x = 0; $x < $count; $x++) {
            $empty_lines .= "\n";
        }
        $data['footer'] = "Yours sincerely" . $empty_lines . User::model()->findByPk(Yii::app()->user->id)->fullName . "\n" . User::model()->findByPk(Yii::app()->user->id)->role . "\n";
        //.(ui.item.consultant?"Consultant: "+ui.item.consultant:'')
        return $data;
    }

    /***
     * Creates the new letter
     *
     * @param $event
     * @param $macro_id
     */
    public function createCorrespondenceContent($event, $macro_id)
    {
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
    public function createNewCorrespondenceEvent($episode_id)
    {
        $event = new Event();
        $event->episode_id = $episode_id;
        $event_type = EventType::model()->find('class_name=:class_name', array(':class_name' => 'OphCoCorrespondence'));
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

    /*
     * @param int $patient_id
     * @param string $contact_string
     * @param boolean $nickname
     */
    public function getAddress($patient_id, $contact_string, $nickname = false)
    {
        if (!$patient = Patient::model()->findByPk($patient_id)) {
            throw new Exception('Unknown patient: ' . $patient_id);
        }

        if (!preg_match('/^([a-zA-Z]+)([0-9]+)$/', $contact_string, $m)) {
            throw new Exception('Invalid contact format: ' . $contact_string);
        }

        if ($m[1] == 'Contact') {
            // NOTE we are assuming that Contact must be a Person model here
            $contact = Person::model()->find('contact_id=?', array($m[2]));
            if ($contact == null) {
                $contact = Contact::model()->findByPk($m[2]);
            }
        } elseif ($m[1] == 'Optometrist') {
            $contact = Contact::model()->findByPk($m[2]);
        } elseif ($m[1] === 'GP') {
            $contact = Gp::model()->findByPk($m[2]);
        } else {
            if (!$contact = $m[1]::model()->findByPk($m[2])) {
                throw new Exception("{$m[1]} not found: {$m[2]}");
            }
        }

        if (method_exists($contact, 'isDeceased') && $contact->isDeceased()) {
            return json_encode(array('errors' => 'DECEASED'));
        }

        $text_ElementLetter_address = $contact->getLetterAddress(array(
            'patient' => $patient,
            'include_name' => true,
            'include_label' => false,
            'delimiter' => "\n",
        ));

        $address = $contact->getLetterAddress(array(
            'patient' => $patient,
            'include_name' => false,
            'include_label' => false,
            'delimiter' => "\n",
        ));

        if (Yii::app()->params['use_contact_practice_associate_model'] === true) {
            if ($m[1] == 'ContactPracticeAssociate') {
                    $contact = $contact->gp;
            }
        }

        if (!$address) {
            $address = '';
        }

        if (!$text_ElementLetter_address) {
            $text_ElementLetter_address = '';
        }

        if (method_exists($contact, 'getCorrespondenceName')) {
            $correspondence_name = $contact->correspondenceName;
        } else {
            $correspondence_name = $contact->fullName;
        }

        if ($m[1] == 'CommissioningBodyService') {
            $correspondence_name = implode(',', $correspondence_name);
        }
        $email = null;
        $contact_type = $m[1];
        if ($m[1] == 'CommissioningBodyService') {
            $contact_type = 'DRSS';
        } elseif ($m[1] == 'Practice') {
            $contact_type = 'Gp';
        } elseif ($m[1] == 'Optometrist') {
            $contact_type = 'Optometrist';
        }

        $contact_id = isset($contact->contact) ? $contact->contact->id : $contact->id;
        $email = isset(Contact::model()->findByPk($contact_id)->id) ? Contact::model()->findByPk($contact_id)->email : null;

        if ( !in_array($contact_type, array('Gp','Patient','DRSS', 'Optometrist' , 'GP')) ) {
            $contact_type = 'Other';
        }

        return $data = array(
            'contact_type' => $contact_type,
            'contact_id' => $contact_id,
            'contact_name' => $correspondence_name,
            'contact_nickname' => isset($contact->contact) ? $contact->contact->nick_name : $contact->nick_name,
            'address' => $address ? $address : "The contact does not have a valid address.",
            'text_ElementLetter_address' => $text_ElementLetter_address,
            'text_ElementLetter_introduction' => $contact->getLetterIntroduction(array(
                'nickname' => (boolean)$nickname,
            )),
            'email' => $email,
        );
    }

    /**
     * Returns the footer text for the correspondence
     *
     * @param User|null $user
     * @param Firm|null $firm
     * @param User|null $consultant
     * @return string
     */
    public function getFooterText(\User $user = null, \Firm $firm = null, \User $consultant = null)
    {
        $user = $user ? $user : \User::model()->findByPk(\Yii::app()->session['user']['id']);
        $firm = $firm ? $firm : \Firm::model()->with('serviceSubspecialtyAssignment')->findByPk(\Yii::app()->session['selected_firm_id']);

        if (!$consultant) {
            // only want a consultant for medical firms or support services such as orthoptics
            if ($specialty = $firm->getSpecialty()) {
                if ($specialty->medical || $specialty->name === 'Support Services') {
                    $consultant = $firm->consultant;
                }
            }
        }

        if ($contact = $user->contact) {
            $service_name = '';
            $consultant_name = false;

            // if we have a consultant for the firm, and its not the matched user, attach the consultant name to the entry
            if ($consultant && ($user->id != $consultant->id)) {
                $service_name = $firm->getServiceText();
                $consultant_name = trim($consultant->contact->title . ' ' . $consultant->contact->first_name . ' ' . $consultant->contact->last_name);
            }

            $full_name = trim($contact->title . ' ' . $contact->first_name . ' ' . $contact->last_name . ' ' . $contact->qualifications);
            return "Yours sincerely\n" . self::ESIGN_PLACEHOLDER . "\n" . $full_name . "\n" . $user->role . "\n" . ($consultant_name ? "Head of " . $service_name . ": " . $consultant_name : '');
        }

        return null;
    }

    /**
     * Returns the Optom portal URL
     * @return string|null
     */
    public function getPortalUrl()
    {
        return isset(Yii::app()->params['portal']['frontend_url']) ? Yii::app()->params['portal']['frontend_url'] : null;
    }

    /*
     * @param int $document_target_id
     * @param $type
     * @param int $letter_id
     */
    public function updateDocumentTargetAddressFromContact($document_target_id, $type, $letter_id)
    {
        $document_target = DocumentTarget::model()->findByPk($document_target_id);
        $contact = Contact::model()->findByPk($document_target->contact_id);
        $patient = $document_target->document_instance->correspondence_event->episode->patient;

        $letter = ElementLetter::model()->findByPk($letter_id);

        if ($letter) {
            foreach (array_keys($letter->address_targets) as $contact_string) {
                $address = $this->getAddress($patient->id, $contact_string);

                if ($address['contact_type'] == $type) {
                    $document_target->contact_name = $address['contact_name'];
                    $document_target->address = $address['address'];
                    $document_target->contact_id = $address['contact_id'];

                    $document_target->save();
                }
            }
        }
    }

    /*
     * Glaucoma Overall Management Plan from latest Examination
     * @param $patient
     * @return string
     */
    public function getGlaucomaManagement(\Patient $patient)
    {
        $result = '';
        $episode = $patient->getEpisodeForCurrentSubspecialty();
        $event_type = EventType::model()->find('class_name=?', array('OphCiExamination'));

        if ($el = $this->getMostRecentElementInEpisode(
            $episode->id,
            $event_type->id,
            'OEModule\OphCiExamination\models\Element_OphCiExamination_OverallManagementPlan'
        )
        ) {
            $result .= 'Clinic Interval: ' . $el->clinic_internal->name . "\n";
            $result .= 'Photo: ' . $el->photo->name . "\n";
            $result .= 'OCT: ' . $el->oct->name . "\n";
            $result .= 'Visual Fields: ' . $el->hfa->name . "\n";
            $result .= 'Gonioscopy: ' . $el->gonio->name . "\n";
            $result .= 'HRT: ' . $el->hrt->name . "\n";

            if (!empty($el->comments)) {
                $result .= 'Glaucoma Management comments: ' . $el->comments . "\n";
            }

            $result .= "\n";
            if (isset($el->right_target_iop->name)) {
                $result .= 'Target IOP Right Eye: ' . $el->right_target_iop->name . " mmHg\n";
            }
            if (isset($el->left_target_iop->name)) {
                $result .= 'Target IOP Left Eye: ' . $el->left_target_iop->name . " mmHg\n";
            }
        }
        return $result;
    }

    public function getLastExaminationInSs(\Patient $patient)
    {
        $api = $this->yii->moduleAPI->get('OphCiExamination');
        $event = $api->getLatestEvent($patient, true);

        if (isset($event)) {
            return json_encode(array(
                'id' => $event->id,
                'event_date' => Helper::convertDate2NHS($event->event_date),
                'event_name' => $event->eventType->name
            ));
        }
        return '';
    }

    public function getLastOpNoteInSs(\Patient $patient)
    {
        $api = $this->yii->moduleAPI->get('OphTrOperationnote');
        $event = $api->getLatestEvent($patient, true);

        if (isset($event)) {
            return json_encode(array(
                'id' => $event->id,
                'event_date' => Helper::convertDate2NHS($event->event_date),
                'event_name' => $event->eventType->name
            ));
        }
        return '';
    }

    public function getLastEventInSs(\Patient $patient)
    {
    }

    public function getLastInjectionInSs(\Patient $patient)
    {
        $api = $this->yii->moduleAPI->get('OphTrIntravitrealinjection');
        $event = $api->getLatestEvent($patient, true);

        if (isset($event)) {
            return json_encode(array(
                'id' => $event->id,
                'event_date' => Helper::convertDate2NHS($event->event_date),
                'event_name' => $event->eventType->name
            ));
        }
        return '';
    }

    public function getLastLaserInSs(\Patient $patient)
    {
        $api = $this->yii->moduleAPI->get('OphTrLaser');
        $event = $api->getLatestEvent($patient, true);

        if (isset($event)) {
            return json_encode(array(
                'id' => $event->id,
                'event_date' => Helper::convertDate2NHS($event->event_date),
                'event_name' => $event->eventType->name
            ));
        }
        return '';
    }

    public function getLastPrescriptionInSs(\Patient $patient)
    {
        $api = $this->yii->moduleAPI->get('OphDrPrescription');
        $event = $api->getLatestEvent($patient, true);

        if (isset($event)) {
            return json_encode(array(
                'id' => $event->id,
                'event_date' => Helper::convertDate2NHS($event->event_date),
                'event_name' => $event->eventType->name
            ));
        }
        return '';
    }

    public function getNickname($identify_with)
    {
        if (is_numeric($identify_with)) {
            $contact_id = $identify_with;
        }

        if (isset($contact_id)) {
            $contact = Contact::model()->find('id=?', array($contact_id));
            if ($contact) {
                return $contact->nick_name;
            }
        }

        return;
    }

    public function getDefaultMacroByEpisodeStatus(\Episode $episode, $firm = null, $site_id = null, $macro_name = null)
    {
        if (empty($macro_name)) {
            $macro_name = \SettingMetadata::model()->getSetting("default_{$episode->status->key}_letter");
        }

        return $this->getDefaultMacro($firm, $site_id, $macro_name);
    }

    public function getDefaultMacro($firm = null, $site_id = null, $macro_name = null)
    {
        $macro = LetterMacro::model()->with('firms')->find('t.name = ? AND firms_firms.firm_id = ?', [$macro_name, $firm->id]);

        if (!$macro) {
            if ($firm->service_subspecialty_assignment_id) {
                $subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;

                $macro = LetterMacro::model()->with('subspecialties')->find('subspecialties_subspecialties.subspecialty_id = ? AND t.name = ?', [$subspecialty_id, $macro_name]);
                if (!$macro) {
                    $macro = LetterMacro::model()->with('sites', 'institutions')->find('(institutions_institutions.institution_id = ? OR sites_sites.site_id = ?) AND t.name = ?', [Yii::app()->session['selected_institution_id'], $site_id, $macro_name]);
                }
            }
        }

        return $macro;
    }
}
