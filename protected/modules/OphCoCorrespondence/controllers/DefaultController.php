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
class DefaultController extends BaseEventTypeController
{
    protected static $action_types = array(
        'getAddress' => self::ACTION_TYPE_FORM,
        'getMacroData' => self::ACTION_TYPE_FORM,
        'getString' => self::ACTION_TYPE_FORM,
        'getCc' => self::ACTION_TYPE_FORM,
        'getConsultantsBySubspecialty' => self::ACTION_TYPE_FORM,
        'getSalutationByFirm' => self::ACTION_TYPE_FORM,
        'getSiteInfo' => self::ACTION_TYPE_FORM,
        'expandStrings' => self::ACTION_TYPE_FORM,
        'users' => self::ACTION_TYPE_FORM,
        'doPrint' => self::ACTION_TYPE_PRINT,
        'markPrinted' => self::ACTION_TYPE_PRINT,
        'doPrintAndView' => self::ACTION_TYPE_PRINT,
    );

    protected $show_element_sidebar = false;

    /**
     * Adds direct line phone numbers to jsvars to be used in dropdown select.
     */
    public function loadDirectLines()
    {
        $sfs = FirmSiteSecretary::model()->findAll('firm_id=?', array(Yii::app()->session['selected_firm_id']));
        $vars[] = null;
        foreach ($sfs as $sf) {
            $vars[$sf->site_id] = $sf->direct_line;
        }

        $this->jsVars['correspondence_directlines'] = $vars;
    }

    /**
     * Set up some key js vars.
     *
     * @param string $action
     */
    protected function initAction($action)
    {
        parent::initAction($action);
        $this->jsVars['electronic_sending_method_label'] = Yii::app()->params['electronic_sending_method_label'];

        $site = Site::model()->findByPk( Yii::app()->session['selected_site_id'] );

        $this->jsVars['internal_referral_booking_address'] = $site->getCorrespondenceName();

        $this->jsVars['internal_referral_method_label'] = ElementLetter::model()->getInternalReferralSettings('internal_referral_method_label');

        $event_id = Yii::app()->request->getQuery('id');
        if($event_id){
            $letter = ElementLetter::model()->find('event_id=?', array($event_id));
            $this->editable = $letter->isEditable();
            $api = Yii::app()->moduleAPI->get('OphCoCorrespondence');

            if($action == 'update'){
                if( !Yii::app()->request->isPostRequest && $letter->draft){
                    
                    $gp_targets = $letter->getTargetByContactType("GP");
              
                    foreach($gp_targets as $gp_target){
                        $api->updateDocumentTargetAddressFromContact($gp_target->id, 'Gp', $letter->id);
                    }
                }
            }
        }

        if (in_array($action, array('create', 'update'))) {
            $this->jsVars['OE_gp_id'] = $this->patient->gp_id;
            $this->jsVars['OE_practice_id'] = $this->patient->practice_id;
            $this->jsVars['OE_site_id'] = Yii::app()->session['selected_site_id'];

            $to_location = OphCoCorrespondence_InternalReferral_ToLocation::model()->findByAttributes(
                array('site_id' => Yii::app()->session['selected_site_id'],
                      'is_active' => 1)
            );

            $this->jsVars['OE_to_location_id'] = $to_location ? $to_location->id : null;

            $this->getApp()->assetManager->registerScriptFile('js/docman.js');
            
            $this->loadDirectLines();
        }       
        
    }

    /**
     * Set up some js vars.
     */
    public function initActionView()
    {
        parent::initActionView();
        $this->jsVars['correspondence_markprinted_url'] = Yii::app()->createUrl('OphCoCorrespondence/Default/markPrinted/'.$this->event->id);
        $this->jsVars['correspondence_print_url'] = Yii::app()->createUrl('OphCoCorrespondence/Default/print/'.$this->event->id);
    }
    
    public function actionView($id)
    {
        $letter = ElementLetter::model()->find('event_id=?', array($id));

        $output = $letter->getOutputByType(['Docman', 'Internalreferral']);
        if($output){
            $docnam = $output[0]; //for now only one Docman allowed
            $title = $docnam->output_status;
            if($docnam->output_status == 'COMPLETE'){
                $title = 'Sent';
            }
            $title = strtolower($title);
            $this->title .= ' (' . ucfirst($title) . ')';
        }
        parent::actionView($id);
    }
    
    public function actionUpdate($id)
    {
        $letter = ElementLetter::model()->find('event_id=?', array($id));

        // admin can go to edit mode event if the document has been sent
        if(!$letter->isEditable()){
            $this->redirect(array('default/view/'.$id));
        }

        //if the letter is generated than we set a warning (only admin should reach this point, handled in $letter->isEditable())
        if( $letter->isGeneratedFor(['Docman', 'Internalreferral']) ){
            Yii::app()->user->setFlash('warning.letter_warning', 'Please note this letter has already been sent. Only modify if it is really necessary!');
        }

        parent::actionUpdate($id);
    }
    
    /**
     * Ajax action to get the address for a contact.
     *
     * @throws Exception
     */
    public function actionGetAddress()
    {
        
        
        if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
            throw new Exception('Unknown patient: '.@$_GET['patient_id']);
        }

        if (!preg_match('/^([a-zA-Z]+)([0-9]+)$/', @$_GET['contact'], $m)) {
            throw new Exception('Invalid contact format: '.@$_GET['contact']);
        }
        
        $api = Yii::app()->moduleAPI->get('OphCoCorrespondence');
        $data = $api->getAddress($_GET['patient_id'], $_GET['contact']);
        echo json_encode($data);
        
        return;
    }

    /**
     * Ajax action to get macro data for populating the letter elements.
     *
     * @throws Exception
     */
    public function actionGetMacroData()
    {
        if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
            throw new Exception('Patient not found: '.@$_GET['patient_id']);
        }

        if (!$macro = LetterMacro::model()->findByPk(@$_GET['macro_id'])) {
            throw new Exception('Macro not found: '.@$_GET['macro_id']);
        }

        $data = array();

        $macro->substitute($patient);

        if ($macro->recipient && $macro->recipient->name == 'Patient') {
            $data['sel_address_target'] = 'Patient'.$patient->id;
            $contact = $patient;
            if ($patient->isDeceased()) {
                echo json_encode(array('error' => 'DECEASED'));

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

            if ($address) {
                $data['text_ElementLetter_address'] = $address;
            } else {
                $data['alert'] = 'The contact does not have a valid address.';
                $data['text_ElementLetter_address'] = '';
            }

            $data['text_ElementLetter_introduction'] = $contact->getLetterIntroduction(array(
                'nickname' => $macro->use_nickname,
            ));
        }

        $data['check_ElementLetter_use_nickname'] = $macro->use_nickname;

        if ($macro->body) {
            $data['text_ElementLetter_body'] = $macro->body;
        }

        $cc = array(
            'text' => array(),
            'targets' => array(),
        );
        if ($macro->cc_patient) {
            if ($patient->isDeceased()) {
                $data['alert'] = "Warning: the patient cannot be cc'd because they are deceased.";
            } elseif ($patient->contact->address) {
                $cc['text'][] = $patient->getLetterAddress(array(
                    'include_name' => true,
                    'include_label' => true,
                    'delimiter' => ', ',
                    'include_prefix' => true,
                ));
                $cc['targets'][] = '<input type="hidden" name="CC_Targets[]" value="Patient'.$patient->id.'" />';
            } else {
                $data['alert'] = "Letters to the GP should be cc'd to the patient, but this patient does not have a valid address.";
            }
        }

        if ($macro->cc_doctor && $cc_contact = ($patient->gp) ? $patient->gp : $patient->practice) {
            $cc['text'][] = $cc_contact->getLetterAddress(array(
                    'patient' => $patient,
                    'include_name' => true,
                    'include_label' => true,
                    'delimiter' => ', ',
                    'include_prefix' => true,
                ));
            $cc['targets'][] = '<input type="hidden" name="CC_Targets[]" value="'.get_class($cc_contact).$cc_contact->id.'" />';
        }

        if ($macro->cc_drss) {
            $commissioningbodytype = CommissioningBodyType::model()->find('shortname = ?', array('CCG'));
            if ($commissioningbodytype && $commissioningbody = $patient->getCommissioningBodyOfType($commissioningbodytype)) {
                $drss = null;
                foreach ($commissioningbody->services as $service) {
                    if ($service->type->shortname == 'DRSS') {
                        $cc['text'][] = $service->getLetterAddress(array(
                                'include_name' => true,
                                'include_label' => true,
                                'delimiter' => ', ',
                                'include_prefix' => true,
                            ));
                        $cc['targets'][] = '<input type="hidden" name="CC_Targets[]" value="CommissioningBodyService'.$service->id.'" />';
                        break;
                    }
                }
            }
        }

        $data['textappend_ElementLetter_cc'] = implode("\n", $cc['text']);
        $data['elementappend_cc_targets'] = implode("\n", $cc['targets']);
        $data['sel_letter_type_id'] = $macro->letter_type_id;
        echo json_encode($data);
    }

    /**
     * Ajax action to process a selected string request.
     *
     * @throws Exception
     */
    public function actionGetString()
    {
        if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
            throw new Exception('Patient not found: '.@$_GET['patient_id']);
        }

        switch (@$_GET['string_type']) {
            case 'site':
                if (!$string = LetterString::model()->findByPk(@$_GET['string_id'])) {
                    throw new Exception('Site letter string not found: '.@$_GET['string_id']);
                }
                break;
            case 'subspecialty':
                if (!$string = SubspecialtyLetterString::model()->findByPk(@$_GET['string_id'])) {
                    throw new Exception('Subspecialty letter string not found: '.@$_GET['string_id']);
                }
                break;
            case 'firm':
                if (!$firm = FirmLetterString::model()->findByPk(@$_GET['string_id'])) {
                    throw new Exception('Firm letter string not found: '.@$_GET['string_id']);
                }
                break;
            case 'examination':
                echo $this->process_examination_findings($_GET['patient_id'], $_GET['string_id']);

                return;
            default:
                throw new Exception('Unknown letter string type: '.@$_GET['string_type']);
        }

        $string->substitute($patient);

        echo $string->body;
    }

    /**
     * Ajax action to get cc contact details.
     *
     * @throws Exception
     */
    public function actionGetCc()
    {
        if (!$patient = Patient::model()->findByPk(@$_GET['patient_id'])) {
            throw new Exception('Unknown patient: '.@$_GET['patient_id']);
        }

        if (!preg_match('/^([a-zA-Z]+)([0-9]+)$/', @$_GET['contact'], $m)) {
            throw new Exception('Invalid contact format: '.@$_GET['contact']);
        }

        if ($m[1] == 'Contact') {
            $contact = Person::model()->find('contact_id=?', array($m[2]));
        } else {
            if (!$contact = $m[1]::model()->findByPk($m[2])) {
                throw new Exception("{$m[1]} not found: {$m[2]}");
            }
        }

        if ($contact->isDeceased()) {
            echo json_encode(array('errors' => 'DECEASED'));

            return;
        }

        $address = $contact->getLetterAddress(array(
            'patient' => $patient,
            'include_name' => true,
            'include_label' => true,
            'delimiter' => '| ',
            'include_prefix' => true,
        ));

        $address = str_replace(',', ';', $address);
        $address = str_replace('|', ',', $address);

        echo $address ? $address : 'NO ADDRESS';
    }

    /**
     * Ajax action to expand shortcodes in letter string for a patient.
     *
     * @throws Exception
     */
    public function actionExpandStrings()
    {
        if (!$patient = Patient::model()->findByPk(@$_POST['patient_id'])) {
            throw new Exception('Patient not found: '.@$_POST['patient_id']);
        }

        $text = @$_POST['text'];
        $textNew = OphCoCorrespondence_Substitution::replace($text, $patient);

        if ($text != $textNew) {
            echo $textNew;
        }
    }

    /**
     * Ajax action to mark a letter as printed.
     *
     * @param $id
     *
     * @throws Exception
     */
    public function actionMarkPrinted($id)
    {
        if ($letter = ElementLetter::model()->find('event_id=?', array($id))) {
            $letter->print = 0;
            if (!$letter->save()) {
                throw new Exception('Unable to mark letter printed: '.print_r($letter->getErrors(), true));
            }
        }
    }
    
    public function actionPrint($id)
    {
        $letter = ElementLetter::model()->find('event_id=?', array($id));

        $this->printInit($id);
        $this->layout = '//layouts/print';
        
        // after "Save and Print" button clicked we only print out what the user checked
        if( isset($_GET['OphCoCorrespondence_print_checked']) && $_GET['OphCoCorrespondence_print_checked'] == "1" ){
            
            // check if the first recipient is GP
            $docunemt_instance = $letter->document_instance[0];
            $to_recipient_gp = DocumentTarget::model()->find('document_instance_id=:id AND ToCc=:ToCc AND (contact_type=:type_gp OR contact_type=:type_ir)',array(
                ':id' => $docunemt_instance->id, ':ToCc' => 'To', ':type_gp' => 'GP', ':type_ir' => 'INTERNALREFERRAL', ));
            
            if($to_recipient_gp){
                // print an extra copy to note
                if(!Yii::app()->params['disable_correspondence_notes_copy']) {
                    $this->render('print', array(
                        'element' => $letter,
                        'letter_address' => ($to_recipient_gp->contact_name . "\n" . $to_recipient_gp->address)
                    ));
                }
            }

            $print_outputs = $letter->getOutputByType("Print");
            if($print_outputs){
                foreach($print_outputs as $print_output){
                    $document_target = DocumentTarget::model()->findByPk($print_output->document_target_id);
                    $this->render('print', array('element' => $letter, 'letter_address' => ($document_target->contact_name . "\n" . $document_target->address)));
                    
                    //extra printout for note
                    if($document_target->ToCc == 'To' && $document_target->contact_type != 'GP'){
                        if(!Yii::app()->params['disable_correspondence_notes_copy']){
                            $this->render('print', array('element' => $letter, 'letter_address' => ($document_target->contact_name . "\n" . $document_target->address)));
                        }
                    }
                }
            }

        } else {

            /**
             * this is a hotfix for DocMan, when we generate correspondences
             * where the main recipient is NOT the GP than we need to cherrypick it
             */
            if( isset($_GET['print_only_gp']) && $_GET['print_only_gp'] == "1" ){

                $gp_targets = $letter->getTargetByContactType("GP");
                foreach($gp_targets as $gp_target){
                    $this->render('print', array('element' => $letter, 'letter_address' => $gp_target->contact_name . "\n" . $gp_target->address ));
                }

                return;
            }
            
            $this->render('print', array('element' => $letter));

            if ($this->pdf_print_suffix == 'all' || @$_GET['all']) {
                if(!Yii::app()->params['disable_correspondence_notes_copy']) {
                    $this->render('print', array('element' => $letter));
                }

                foreach ($letter->getCcTargets() as $letter_address) {
                    $this->render('print', array('element' => $letter, 'letter_address' => $letter_address));
                }
            }
        }
    }

    public function actionPDFPrint($id)
    {
        $letter = ElementLetter::model()->find('event_id=?', array($id));
        $print_outputs = $letter->getOutputByType("Print");

        if (Yii::app()->request->getQuery('all', false)) {
            $this->pdf_print_suffix = 'all';
            $this->pdf_print_documents = 2 + count($letter->getCcTargets());
        }
        if (Yii::app()->request->getQuery('OphCoCorrespondence_print_checked', false)) {
            $this->pdf_print_suffix = 'all';
            $this->pdf_print_documents = count($print_outputs);
        }
        
        if( $print_outputs ){
            foreach($print_outputs as $output){
                $output->output_status = "COMPLETE";
                $output->save();
            }
        }


        return parent::actionPDFPrint($id);
    }
    
    /**
     * Ajax action to get user data list.
     */
    public function actionUsers()
    {
        $users = array();

        $criteria = new CDbCriteria();

        $criteria->addCondition(array('active = :active'));
        $criteria->addCondition(array("LOWER(concat_ws(' ',first_name,last_name)) LIKE :term"));

        $params[':active'] = 1;
        $params[':term'] = '%'.strtolower(strtr($_GET['term'], array('%' => '\%'))).'%';

        $criteria->params = $params;
        $criteria->order = 'first_name, last_name';

        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        $consultant = null;
        // only want a consultant for medical firms
        if ($specialty = $firm->getSpecialty()) {
            if ($specialty->medical) {
                $consultant = $firm->consultant;
            }
        }

        foreach (User::model()->findAll($criteria) as $user) {
            if ($contact = $user->contact) {
                $consultant_name = false;

                // if we have a consultant for the firm, and its not the matched user, attach the consultant name to the entry
                if ($consultant && $user->id != $consultant->id) {
                    $consultant_name = trim($consultant->contact->title.' '.$consultant->contact->first_name.' '.$consultant->contact->last_name);
                }

                $users[] = array(
                    'id' => $user->id,
                    'value' => trim($contact->title.' '.$contact->first_name.' '.$contact->last_name.' '.$contact->qualifications).' ('.$user->role.')',
                    'fullname' => trim($contact->title.' '.$contact->first_name.' '.$contact->last_name.' '.$contact->qualifications),
                    'role' => $user->role,
                    'consultant' => $consultant_name,
                );
            }
        }

        echo json_encode($users);
    }

    /**
     * Use the examination API to retrieve findings for the patient and element type.
     *
     * @param $patient_id
     * @param $element_type_id
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function process_examination_findings($patient_id, $element_type_id)
    {
        if ($api = Yii::app()->moduleAPI->get('OphCiExamination')) {
            if (!$patient = Patient::model()->findByPk($patient_id)) {
                throw new Exception('Unable to find patient: '.$patient_id);
            }

            if (!$element_type = ElementType::model()->findByPk($element_type_id)) {
                throw new Exception("Unknown element type: $element_type_id");
            }

            return $api->getLetterStringForModel($patient, $element_type_id);
        }
    }

    /**
     * Sets a letter element to print when it's next viewed.
     *
     * @param $id
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function setPrintForEvent($id)
    {
        if (!$letter = ElementLetter::model()->find('event_id=?', array($id))) {
            throw new Exception("Letter not found for event id: $id");
        }
                
        $letter->print = 1;

        if (@$_GET['all']) {
            $letter->print_all = 1;
        }

        if (!$letter->save()) {
            throw new Exception('Unable to save letter: '.print_r($letter->getErrors(), true));
        }

        if (!$event = Event::model()->findByPk($id)) {
            throw new Exception("Event not found: $id");
        }

        $event->info = '';

        if (!$event->save()) {
            throw new Exception('Unable to save event: '.print_r($event->getErrors(), true));
        }

        return true;
    }

    /**
     * Wrapper action to mark letter for printing and then view the letter to trigger
     * printing behaviour client side.
     *
     * @param $id
     */
    public function actionDoPrintAndView($id)
    {
        if ($this->setPrintForEvent($id)) {
            $this->redirect(array('default/view/'.$id));
        }
    }

    /**
     * Ajax action to mark letter for printing.
     *
     * @param $id
     *
     * @throws Exception
     */
    public function actionDoPrint($id)
    {
        if ($this->setPrintForEvent($id)) {
            echo '1';
        }
    }

    /**
     * Returns the consultants by subspecialty
     * @param null $subspecialty_id
     */
    public function actionGetConsultantsBySubspecialty($subspecialty_id = null)
    {
        $firms = Firm::model()->getListWithSpecialties(false, $subspecialty_id);
        echo CJSON::encode($firms);

        Yii::app()->end();
    }

    /**
     * Returns the consultant's or subspecialty salutation
     * @param $firm_id
     * @throws Exception when firm not found by ID
     * @return json salutation
     */
    public function actionGetSalutationByFirm($firm_id)
    {
        $firm = Firm::model()->findByPk($firm_id);

        if(!$firm){
            throw new Exception("Firm not found. ID: $firm_id");
        }
        $user = User::model()->findByPk($firm->consultant_id);

        if($user){
            $salutation = $user->getSalutationName() . " ({$firm->getSubspecialtyText()}),";
        } else {
            $salutation = 'Dear ' . $firm->getSubspecialtyText() . ' Service,';
        }

        echo CJSON::encode($salutation);
        Yii::app()->end();
    }

    public function actionGetSiteInfo($to_location_id)
    {
        $to_location = OphCoCorrespondence_InternalReferral_ToLocation::model()->findByPk($to_location_id);
        $site = $to_location->site;

        $attributes = $site->attributes;
        $attributes['correspondence_name'] = $site->getCorrespondenceName();
        echo CJSON::encode($attributes);

        Yii::app()->end();
    }

}
