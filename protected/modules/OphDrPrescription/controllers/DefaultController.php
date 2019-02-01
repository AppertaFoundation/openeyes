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
    protected $show_element_sidebar = false;

    protected static $action_types = array(
        'drugList' => self::ACTION_TYPE_FORM,
        'repeatForm' => self::ACTION_TYPE_FORM,
        'routeOptions' => self::ACTION_TYPE_FORM,
        'doPrint' => self::ACTION_TYPE_PRINT,
        'markPrinted' => self::ACTION_TYPE_PRINT,
        'printCopy'    => self::ACTION_TYPE_PRINT,
        'finalize' => self::ACTION_TYPE_FORM,
    );

    private function userIsAdmin()
    {
        $user = Yii::app()->session['user'];

        if (Yii::app()->authManager->checkAccess('admin', $user->id)) {
            return true;
        }

        return false;
    }

    public function actionView($id)
    {
        $model = Element_OphDrPrescription_Details::model()->findBySql('SELECT * FROM et_ophdrprescription_details WHERE event_id = :id', [':id'=>$id]);
        
        $this->editable = $model->isEditableByMedication();
        if( $this->editable == true ){
            $this->editable = $this->userIsAdmin() || $model->draft || (SettingMetadata::model()->findByAttributes(array('key' => 'enable_prescriptions_edit'))->getSettingName() === 'On');
        }
        return parent::actionView($id);
    }

    /**
     * Defines JS data structure for common drug lookup in prescription.
     */
    protected function setCommonDrugMetadata()
    {
        $this->jsVars['common_drug_metadata'] = array();
        foreach (Element_OphDrPrescription_Details::model()->commonDrugs() as $medication) {
            $this->jsVars['common_drug_metadata'][$medication->id] = array(
                    'medication_set_id' => array_map(function($e){ return $e->id; }, $medication->getTypes()),
                    'preservative_free' => (int)$medication->isPreservativeFree(),
            );
        }
    }

    protected function initEdit()
    {
        if (!$this->checkPrintAccess()) {
            return false;
        }

        $assetManager = Yii::app()->getAssetManager();
        $baseAssetsPath = Yii::getPathOfAlias('application.assets.js');
        $assetManager->publish($baseAssetsPath);
        Yii::app()->clientScript->registerScriptFile($assetManager->getPublishedUrl($baseAssetsPath).'/OpenEyes.UI.InputFieldValidation.js', CClientScript::POS_END);

        $this->setCommonDrugMetadata();
        $this->showAllergyWarning();
        // Save and print clicked, stash print flag
        if (isset($_POST['saveprint'])) {
            Yii::app()->session['print_prescription'] = true;
        }
    }

    public function printActions()
    {
        return array('print', 'markPrinted', 'doPrint');
    }

    /**
     * Initialisation function for specific patient id
     * Used for ajax actions.
     *
     * @param $patient_id
     *
     * @throws CHttpException
     */
    protected function initForPatient($patient_id)
    {
        if (!$this->patient = Patient::model()->findByPk($patient_id)) {
            throw new CHttpException(403, 'Invalid patient_id.');
        }

        if (!$this->episode = $this->getEpisode($this->firm, $patient_id)) {
            throw new CHttpException(403, 'Invalid request for this patient.');
        }
    }

    /**
     * Some additional initialisation for create.
     */
    protected function initActionCreate()
    {
        parent::initActionCreate();
        $this->initEdit();
    }

    /**
     * Some additional initialisation for create.
     */
    protected function initActionUpdate()
    {
        parent::initActionUpdate();
        $this->initEdit();

    }

    /**
     * Some additional initialisation for view.
     */
    protected function initActionView()
    {
        parent::initActionView();

        // Clear any stale warning
        Yii::app()->user->getFlash('warning.prescription_allergy');

        // set required js variables
        $cs = Yii::app()->getClientScript();
        $cs->registerScript('scr_prescription_view',
            "prescription_print_url = '".Yii::app()->createUrl('/OphDrPrescription/default/print/'.$this->event->id)."';\n", CClientScript::POS_READY);

        // Get prescription details element
        $element = Element_OphDrPrescription_Details::model()->findByAttributes(array('event_id' => $this->event->id));

        foreach ($element->items as $item) {
            if ($this->patient->hasDrugAllergy($item->medication_id)) {
                $this->showAllergyWarning();
                break;
            }
        }
    }

    /**
     * marks the prescription as printed. So this is the 3rd place this can happen, but not sure
     * if this is every called.
     *
     * @param int $id
     *
     * @throws Exception
     */
    public function printInit($id)
    {
        parent::printInit($id);
        if (!$prescription = Element_OphDrPrescription_Details::model()->find('event_id=?', array($id))) {
            throw new Exception('Prescription not found: '.$id);
        }
        $prescription->printed = 1;
        if (!$prescription->save()) {
            throw new Exception('Unable to save prescription: '.print_r($prescription->getErrors(), true));
        }
        $this->event->info = $prescription->infotext;
        if (!$this->event->save()) {
            throw new Exception('Unable to save event: '.print_r($this->event->getErrors(), true));
        }
    }

    /**
     * Set prescription item defaults when creating.
     *
     * @param BaseEventTypeElement $element
     * @param string               $action
     */
    protected function setElementDefaultOptions($element, $action)
    {
        parent::setElementDefaultOptions($element, $action);
        if ($action == 'create' && get_class($element) == 'Element_OphDrPrescription_Details') {
            // Prepopulate prescription with set by episode status
            // FIXME: It's brittle relying on the set name matching the status
            $items = array();
            $status_name = $this->episode->status->name;
            $subspecialty_id = $this->firm->getSubspecialtyID();
            $params = array(':subspecialty_id' => $subspecialty_id, ':status_name' => $status_name);

            $set = MedicationSet::model()->with('medicationSetRules')->find(array(
                'condition' => 'medicationSetRules.subspecialty_id = :subspecialty_id AND t.name = :status_name',
                'params' => $params,
            ));

            if ($set) {
                foreach ($set->items as $item) {
                    $item_model = new OphDrPrescription_Item();
                    $item_model->medication_id = $item->id;
                    $item_model->loadDefaults($set);
                    $attr = $item->getAttributes();
                    unset($attr['drug_set_id']);
                    $item_model->attributes = $attr;

                    $item_model->tapers = $item->tapers;

                    if ($api = Yii::app()->moduleAPI->get('OphTrOperationnote')) {
                        if ($apieye = $api->getLastEye($this->patient, false)) {
                            $item_model->laterality = $apieye;
                        }
                    }

                    $items[] = $item_model;
                }
            }
            $element->items = $items;
        }
    }

    /**
     * Set flash message for patient allergies.
     */
    protected function showAllergyWarning()
    {
        if ($this->patient->no_allergies_date) {
            Yii::app()->user->setFlash('info.prescription_allergy', $this->patient->getAllergiesString());
        } else {
            Yii::app()->user->setFlash('patient.prescription_allergy', $this->patient->getAllergiesString());
        }
    }

    /*
     * Set flash message reason for edit
     * @param $reason_id
     * @param $reason_text
     */
    protected function showReasonForEdit( $reason_id, $reason_text )
    {
        $edit_reason = OphDrPrescriptionEditReasons::model()->findByPk($reason_id);
        if($edit_reason != null){
            if($reason_id > 1){
                Yii::app()->user->setFlash('alert.edit_reason', 'Edit reason: '.$edit_reason->caption);
            } else {
                Yii::app()->user->setFlash('alert.edit_reason', 'Edit reason: '.$reason_text);
            }
        }
    }

    /**
     * Ajax action to search for drugs.
     */
    public function actionDrugList()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $criteria = new CDbCriteria();

            $criteria->addCondition('deleted_date IS NULL');

            $params = [];
            $return = array();

            if (isset($_GET['term']) && strlen($term = $_GET['term']) > 0) {
                $criteria->addCondition('id IN (SELECT medication_id FROM medication_search_index WHERE LOWER(alternative_term) LIKE :term)');
                $params[':term'] = '%'.strtolower(strtr($term, array('%' => '\%'))).'%';
            }
            if (isset($_GET['type_id']) && $type_id = $_GET['type_id']) {

                $criteria->addCondition("id IN (SELECT medication_id FROM medication_set_item WHERE medication_set_id = :type_id)");
                $params[':type_id'] = $type_id;
            }
            if (isset($_GET['preservative_free']) && $preservative_free = $_GET['preservative_free']) {
                $criteria->addCondition("id IN (SELECT medication_id FROM medication_set_item WHERE 
                                                medication_set_id = (SELECT id FROM medication_set WHERE name = 'Preservative free'))");
            }

            if(!empty($criteria->condition)){
                $criteria->order = 'preferred_term';
                $criteria->limit = 10;
                $criteria->select = 'id, preferred_term';
                $criteria->params = $params;

                $drugs = Medication::model()->findAll($criteria);


                foreach ($drugs as $drug) {

                    $infoBox = new MedicationInfoBox();
                    $infoBox->medication_id = $drug->id;
                    $infoBox->init();
                    $tooltip = $infoBox->getHTML();

                    $return[] = array(
                        'label' => $drug->preferred_term,
                        'value' => $drug->preferred_term,
                        'id' => $drug->id,
                        'prepended_markup' => $tooltip
                    );
                }
            }

            echo CJSON::encode($return);
        }
    }

    /**
     * Get a repeat prescription form for the patient (will ignore prescriptions from the
     * event defined by $current_id if given.
     *
     * @param $key
     * @param $patient_id
     * @param null $current_id
     */
    public function actionRepeatForm($key, $patient_id, $current_id = null)
    {
        $this->initForPatient($patient_id);

        if ($prescription = $this->getPreviousPrescription($current_id)) {
            foreach ($prescription->items as $item) {
                $this->renderPrescriptionItem($key, $item);
                ++$key;
            }
        }
    }

    /**
     * Get the most recent prescription for the current patient, not including
     * those from the given event id $current_id.
     *
     * @param int $current_id - event id to ignore
     *
     * @return Element_OphDrPrescription_Details
     */
    public function getPreviousPrescription($current_id = null)
    {
        if ($this->episode) {
            $condition = 'episode_id = :episode_id';
            $params = array(':episode_id' => $this->episode->id);
            if ($current_id) {
                $condition .= ' AND t.id != :current_id';
                $params[':current_id'] = $current_id;
            }
            $condition .= ' AND event.deleted = 0';

            return Element_OphDrPrescription_Details::model()->find(array(
                    'condition' => $condition,
                    'join' => 'JOIN event ON event.id = t.event_id AND event.deleted = false',
                    'order' => 'created_date DESC',
                    'params' => $params,
            ));
        }
    }

    /**
     * Ajax method to get html dropdown of the options for the given route.
     *
     * @param $key
     * @param $route_id
     */
    public function actionRouteOptions($key, $route_id)
    {
        $route = MedicationRoute::model()->findByPk($route_id);
        if(in_array($route->term, ['Eye', 'Intravitreal'])) {
            $options = MedicationLaterality::model()->findAll('deleted_date IS NULL');
            echo CHtml::dropDownList('Element_OphDrPrescription_Details[items]['.$key.'][laterality]', null, CHtml::listData($options, 'id', 'name'), array('empty' => '-- Select --'));
        }
        else {
            echo '-';
        }
    }

    /**
     * Set the prescription items.
     *
     * @param BaseEventTypeElement $element
     * @param array                $data
     * @param int                  $index
     */
    protected function setElementComplexAttributesFromData($element, $data, $index = null)
    {
        if (get_class($element) == 'Element_OphDrPrescription_Details' && @$data['Element_OphDrPrescription_Details']['items']) {

            // Form has been posted, so we should return the submitted values instead
            $items = array();
            foreach ($data['Element_OphDrPrescription_Details']['items'] as $item) {
                $item_model = new OphDrPrescription_Item();
                $item_model->attributes = $item;
                if($item_model->start_date_string_YYYYMMDD == '') {
                    $item_model->start_date = substr($this->event->event_date, 0, 10);
                }
                if (isset($item['taper'])) {
                    $tapers = array();
                    foreach ($item['taper'] as $taper) {
                        $taper_model = new OphDrPrescription_ItemTaper();
                        $taper_model->attributes = $taper;
                        $tapers[] = $taper_model;
                    }
                    $item_model->tapers = $tapers;
                }
                $items[] = $item_model;
            }

            $element->items = $items;
        }
    }

    /**
     * Actually save the prescription items against the details object.
     *
     * @param $data
     */
    protected function saveEventComplexAttributesFromData($data)
    {
        foreach ($this->open_elements as $element) {
            if (get_class($element) == 'Element_OphDrPrescription_Details') {
                /** @var Element_OphDrPrescription_Details $element */
                $element->updateItems(isset($data['Element_OphDrPrescription_Details']['items']) ? $data['Element_OphDrPrescription_Details']['items'] : array());
            }
        }
    }

    public function actionPrint($id)
    {
        Yii::app()->params['wkhtmltopdf_left_margin'] = '8mm';
        Yii::app()->params['wkhtmltopdf_right_margin'] = '8mm';

        $this->printInit($id);
        $this->layout = '//layouts/print';

        $pdf_documents = (int)Yii::app()->request->getParam('pdf_documents');
        if( $pdf_documents == 1 ){
            $this->render('print');
        } else {
            $this->render('print');
            if(Yii::app()->params['disable_print_notes_copy'] == 'off') {
                $this->render('print', array('copy' => 'notes'));
            }
            if(Yii::app()->params['disable_prescription_patient_copy'] == 'off') {
                $this->render('print', array('copy' => 'patient'));
            }
        }
    }

    public function actionPrintCopy($id)
    {
        $this->actionPrint($id);

        $eventid = 3686356;
        $api = Yii::app()->moduleAPI->get('OphCiExamination');
        $api->printEvent( $eventid );
    }


    public function actionPDFPrint($id)
    {
        $this->pdf_print_suffix = Site::model()->findByPk(Yii::app()->session['selected_site_id'])->id;

        $document_count = 1;
        if(Yii::app()->params['disable_print_notes_copy'] == 'off'){
            $document_count++;
        }

        if(Yii::app()->params['disable_prescription_patient_copy'] == 'off'){
            $document_count++;
        }

        $this->pdf_print_documents = $document_count;

        return parent::actionPDFPrint($id);
    }

    /**
     * Print action for a prescription event, called when a prescription has not ben printed.
     *
     * @param $id
     *
     * @throws Exception
     */
    public function actionDoPrint($id)
    {
        if (!$prescription = Element_OphDrPrescription_Details::model()->find('event_id=?', array($id))) {
            throw new Exception("Prescription not found for event id: $id");
        }

        $prescription->print = 1;
        $prescription->draft = 0;

        if (!$prescription->save()) {
            throw new Exception('Unable to save prescription: '.print_r($prescription->getErrors(), true));
        }

        if (!$event = Event::model()->findByPk($id)) {
            throw new Exception("Event not found: $id");
        }

        // FIXME: this should be using the info method
        $event->info = 'Printed';

        if (!$event->save()) {
            throw new Exception('Unable to save event: '.print_r($event->getErrors(), true));
        }

        echo '1';
    }

    /**
     * Mark a prescription element as printed - called when printing a prescription that has already
     * been printed.
     *
     * @TODO: is this necessary if the print action is already marking the prescription printed?
     *
     * @throws Exception
     */
    public function actionMarkPrinted()
    {
        $event_id = Yii::app()->request->getParam('event_id');
        if(!$event_id){
            throw new Exception('Prescription id not provided');
        }

        if (!$prescription = Element_OphDrPrescription_Details::model()->find('event_id=?', array($event_id))) {
            throw new Exception('Prescription not found for event id: '.$event_id);
        }

        if ($prescription->print == 1) {
            $prescription->print = 0;

            if (!$prescription->save()) {
                throw new Exception('Unable to save prescription: '.print_r($prescription->getErrors(), true));
            }
        }

        $this->printInit($event_id);
        $this->printLog($event_id, false);

        echo '1';
    }

    public function checkCreateAccess()
    {
        return $this->checkAccess('OprnCreatePrescription', $this->firm, $this->episode, $this->event_type);
    }

    public function checkPrintAccess()
    {
        return $this->checkAccess('OprnPrintPrescription');
    }

    public function checkEditAccess()
    {
        return $this->checkAccess('OprnEditPrescription', $this->firm, $this->event);
    }
    
    /**
     * @return MedicationSet
     */

    public function getCommonDrugsRefSet()
    {
        $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
        $subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;
        $site_id = Yii::app()->session['selected_site_id'];
        return MedicationSetRule::model()->findByAttributes(array(
            'subspecialty_id' => $subspecialty_id,
            'site_id' => $site_id,
            'usage_code' => 'Common subspecialty medications'
        ))->medicationSet;
    }

    /**
     * Render the form for a OphDrPrescription_Item, DrugSetItem or Drug (by id).
     *
     * @param $key
     * @param OphDrPrescription_Item|DrugSetItem|int $source
     *
     * @throws CException
     */
    public function renderPrescriptionItem($key, $source)
    {
        $item = new OphDrPrescription_Item();
        if (is_a($source, 'OphDrPrescription_Item')) {

            // Source is a prescription item, so we should clone it
            foreach (array(
                         'medication_id',
                         'duration',
                         'frequency_id',
                         'dose',
                         'laterality',
                         'route_id',
                         'dispense_condition_id',
                         'dispense_location_id'
                     ) as $field) {
                $item->$field = $source->$field;
            }
            if ($source->tapers) {
                $tapers = array();
                foreach ($source->tapers as $taper) {
                    $taper_model = new OphDrPrescription_ItemTaper();
                    $taper_model->dose = $taper->dose;
                    $taper_model->frequency_id = $taper->frequency_id;
                    $taper_model->duration_id = $taper->duration_id;
                    $tapers[] = $taper_model;
                }
                $item->tapers = $tapers;
            }
        } else {
            if (is_a($source, MedicationSetItem::class)) {

                /** @var MedicationSetItem $source */
                $item->medication_id = $source->medication_id;
                $item->frequency_id = $source->default_frequency_id;
                $item->form_id = $source->default_form_id;
                $item->dose = $source->default_dose;
                $item->dose_unit_term = $source->default_dose_unit_term;
                $item->route_id = $source->default_route_id;
                $item->duration = $source->default_duration_id;


                if ($source->tapers) {
                    $tapers = array();
                    foreach ($source->tapers as $taper) {
                        $taper_model = new OphDrPrescription_ItemTaper();
                        foreach (array('duration_id', 'frequency_id', 'dose') as $field) {
                            if ($taper->$field) {
                                $taper_model->$field = $taper->$field;
                            } else {
                                $taper_model->$field = $item->$field;
                            }
                        }
                        $tapers[] = $taper_model;
                    }
                    $item->tapers = $tapers;
                }
            } elseif (is_int($source) || (int) $source) {

                // Source is an integer, so we use it as a drug_id
                $item->medication_id = $source;
                $medSet = $this->getCommonDrugsRefSet();
                $item->loadDefaults($medSet);
            } else {
                throw new CException('Invalid prescription item source: '.print_r($source));
            }

            // Populate route option from episode for Eye
            if ($episode = $this->episode) {
                if ($principal_eye = $episode->eye) {
                    $lat_id = MedicationLaterality::model()->find('name = :eye_name',
                        array(':eye_name' => $principal_eye->name));
                    $item->laterality = ($lat_id) ? $lat_id : null;
                }
                //check operation note eye and use instead of original diagnosis
                if ($api = Yii::app()->moduleAPI->get('OphTrOperationnote')) {
                    if ($apieye = $api->getLastEye($this->patient)) {
                        $item->laterality = $apieye;
                    }
                }
            }
        }
        if (isset($this->patient)) {
            $this->renderPartial('/default/form_Element_OphDrPrescription_Details_Item',
                array('key' => $key, 'item' => $item, 'patient' => $this->patient));
        } else {
            $output = $this->renderPartial('/default/form_Element_OphDrPrescription_Details_Item',
                array('key' => $key, 'item' => $item), true);

            return $output;
        }
    }

    public function actionUpdate($id, $reason = null)
    {
        global $reason_id;
        global $reason_other_text;

        $model = Element_OphDrPrescription_Details::model()->findBySql('SELECT * FROM et_ophdrprescription_details WHERE event_id = :id', [':id'=>$id]);

        if(is_null($reason) && !$model->draft)
        {
            $this->render('ask_reason', array(
                'id'        =>  $id,
                'draft'     => $model->draft,
                'printed'   => $model->printed
            ));
        }
        else
        {
            if(isset($_GET['do_not_save']) && $_GET['do_not_save']=='1')
            {
                $reason_id = isset($_GET['reason']) ? $_GET['reason'] : 0;
                $reason_other_text = isset($_GET['reason_other']) ? $_GET['reason_other'] : '';
               // $_POST=null;
            }
            else
            {
                $reason_id = $model->edit_reason_id;
                $reason_other_text = $model->edit_reason_other;
            }
            $this->showReasonForEdit($reason_id,$reason_other_text);
            parent::actionUpdate($id);
        }
    }
    
    /*
     * Finalize as "Save as final" prescription event, 
     * when a prescription event is created as the result of a medication management element from an examination event
     * 
     * @param       integer     event_id    
     * @param       integer     element_id
     * @return      json_array       
     */
    public function actionFinalize()
    {
        if( Yii::app()->request->isPostRequest ){
            $eventID =  Yii::app()->request->getPost('event');
            $elementID = Yii::app()->request->getPost('element');
            
            $model = Element_OphDrPrescription_Details::model()->findBySql('
                SELECT * FROM et_ophdrprescription_details 
                WHERE id = :id AND event_id = :event_id ', 
                [':event_id'=>$eventID , ':id' => $elementID]
            );

            if($model){
                $model->draft = 0;
                $model->update();
                $result = [
                    'success' => 1
                ];
            } else {
                $result = [
                    'success' => 0
                ];
            }
            
            echo json_encode($result);
        }
       
    }

    /**
     * Group the different kind of drug items for the printout
     *
     * @param $items
     * @return Item[]
     */
    public function groupItems($items)
    {
        $item_group = array();
        foreach($items as $item)
        {
            $item_group[$item->dispense_condition_id][] = $item;
        }
        return $item_group;
    }


    public function getSiteAndTheatreForLatestEvent()
    {
        if($api = Yii::app()->moduleAPI->get('OphTrOperationnote')){
            if($site_theatre = $api->getElementFromLatestEvent('Element_OphTrOperationnote_SiteTheatre', $this->patient, true))
            {
                return $site_theatre;
            }
        }
        return false;
    }
}
