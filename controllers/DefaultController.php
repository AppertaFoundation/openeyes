<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

namespace OEModule\OphCoCvi\controllers;

use \OEModule\OphCoCvi\models;
use \OEModule\OphCoCvi\components\OphCoCvi_Manager;
use \ODTTemplateManager;
use \ODTDataHandler;
use \SignatureQRCodeGenerator;


class DefaultController extends \BaseEventTypeController
{
    public $event_prompt;
    public $cvi_limit = 1;
    protected $cvi_manager;

    const ACTION_TYPE_LIST = 'List';

    protected static $action_types = array(
        'list' => self::ACTION_TYPE_LIST
    );

    /**
     * Create Form with check for the cvi existing events count
     */
    public function actionCreate()
    {
        if (isset($_GET['createnewcvi'])) {
            $cancel_url = ($this->episode) ? '/patient/episode/' . $this->episode->id
                : '/patient/episodes/' . $this->patient->id;
            ($_GET['createnewcvi'] == 1) ? parent::actionCreate()
                : $this->redirect(array($cancel_url));
        } else {
            $cvi_events = $this->getApp()->moduleAPI->get('OphCoCvi');
            $cvi_created = $cvi_events->getEvents(\Patient::model()->findByPk($this->patient->id));
            if (count($cvi_created) >= $this->cvi_limit) {
                $cvi_url = array();
                foreach ($cvi_created as $cvi_event) {
                    $cvi_url[] = $this->getManager()->getEventViewUri($cvi_event);
                }
                $this->render('select_event', array(
                    'cvi_url' => $cvi_url,
                ), false, true);
            } else {
                parent::actionCreate();
            }
        }
    }

    /**
     * Currently uses the OprnEditCvi operation to check for access
     *
     * @return boolean
     */
    public function checkListAccess()
    {
        return $this->checkAccess('OprnEditCvi', $this->getApp()->user->id);
    }

    /**
     * This is a granular permission check, and should be used in conjunection with checkEditAcess
     *
     * @return boolean
     */
    public function checkClericalEditAccess()
    {
        return $this->checkAccess('OprnEditClericalCvi', $this->getApp()->user->id);
    }

    /**
     * This is a granular permission check, and should be used in conjunection with checkEditAcess
     *
     * @return boolean
     */
    public function checkClinicalEditAccess()
    {
        return $this->checkAccess('OprnEditClinicalCvi', $this->getApp()->user->id);
    }

    /**
     * @return bool
     */
    public function checkEditAccess()
    {
        return $this->checkAccess('OprnEditCvi', $this->getApp()->user->id, array(
            'firm' => $this->firm,
            'event' => $this->event
        ));
    }

    /**
     * @return bool
     */
    public function checkCreateAccess()
    {
        return $this->checkAccess('OprnCreateCvi', $this->getApp()->user->id, array(
            'firm' => $this->firm,
            'episode' => $this->episode
        ));
    }

    /**
     * @return bool
     */
    public function checkPrintAccess()
    {
        // check that the user has the general edit cvi permission, but not the specific edit permission on
        // the current event.
        return $this->checkAccess('OprnEditCvi', $this->getApp()->user->id) && $this->getManager()->isIssued($this->event);
    }

    /**
     * Ensure we invoke the CVI RBAC rules around requesting deletion.
     *
     * @return bool
     */
    public function checkRequestDeleteAccess()
    {
        return $this->checkEditAccess() && parent::checkRequestDeleteAccess();
    }

    /**
     * Override as the optional elements should not be rendered until completed through the
     * appropriate access levels.
     *
     * @return null
     */
    public function getOptionalElements()
    {
        return null;
    }

    /**
     * Override because we don't want elements removed from the UI if we have rendered them
     * Optionality is in place to support granular permission structure.
     *
     * @param \BaseEventTypeElement $element
     * @return bool
     */
    public function isRequiredInUI(\BaseEventTypeElement $element)
    {
        return true;
    }

    /**
     * Determine if the current event can be issued
     *
     * @return bool
     */
    public function canIssue()
    {
        if ($this->checkEditAccess()) {
            return $this->getManager()->canIssueCvi($this->event);
        } else {
            return false;
        }
    }

    /**
     * @param models\Element_OphCoCvi_ClinicalInfo $element
     * @param $action
     */
    protected function setElementDefaultOptions_Element_OphCoCvi_ClinicalInfo(
        models\Element_OphCoCvi_ClinicalInfo $element,
        $action
    ) {
        // only populate values into the new element if a clinical user
        if ($this->checkClinicalEditAccess() && $element->isNewRecord) {
            if ($exam_api = $this->getApp()->moduleAPI->get('OphCiExamination')) {
                if ($latest_examination_event = $exam_api->getMostRecentVAElementForPatient($this->patient)) {
                    $element->examination_date = $latest_examination_event['event_date'];
                    $element->best_corrected_right_va = $exam_api->getMostRecentVAForPatient($this->patient, 'right',
                        'aided', $latest_examination_event['element']);
                    $element->best_corrected_left_va = $exam_api->getMostRecentVAForPatient($this->patient, 'left',
                        'aided', $latest_examination_event['element']);
                    $element->unaided_right_va = $exam_api->getMostRecentVAForPatient($this->patient, 'right',
                        'unaided',
                        $latest_examination_event['element']);
                    $element->unaided_left_va = $exam_api->getMostRecentVAForPatient($this->patient, 'left', 'unaided',
                        $latest_examination_event['element']);
                }
            }
        }
    }

    protected function setElementDefaultOptions_Element_OphCoCvi_DemographicInfo()
    {

    }

    /**
     * @return OphCoCvi_Manager
     */
    public function getManager()
    {
        if (!isset($this->cvi_manager)) {
            $this->cvi_manager = new OphCoCvi_Manager($this->getApp());
        }

        return $this->cvi_manager;
    }

    /**
     * @var bool internal flag to indicate a filter has been applied on the list view
     */
    private $is_list_filtered = false;

    /**
     * @return bool
     */
    public function isListFiltered()
    {
        return $this->is_list_filtered;
    }

    /**
     *
     * @return array
     */
    protected function getListFilter()
    {
        $filter = array();

        foreach (array('date_from', 'date_to', 'consultant_ids', 'show_issued') as $key) {
            $val = $this->request->getPost($key, null);
            $filter[$key] = $val;
            if ($val) {
                $this->is_list_filtered = true;
            }
        }
        return $filter;
    }

    /**
     * Generate a list of all the CVI events for clerical use.
     */
    public function actionList()
    {
        $this->layout = '//layouts/main';
        $this->renderPatientPanel = false;

        $filter = $this->getListFilter();

        $dp = $this->getManager()->getListDataProvider($filter);

        $this->render('list', array('dp' => $dp));
    }

    /**
     * @throws \CHttpException
     */
    public function initActionIssue()
    {
        $this->initWithEventId($this->request->get('id'));
        if (!$this->canIssue()) {
            throw new \CHttpException(403, 'Event cannot be issued.');
        }
    }

    /**
     * @param $id
     */
    public function actionIssue($id)
    {
        if ($this->getManager()->issueCvi($this->event, $this->getApp()->user->id)) {
            $this->getApp()->user->setFlash('success', 'The CVI has been successfully generated.');
        } else {
            $this->getApp()->user->setFlash('error', 'The CVI could not be generated.');
        }

        $this->redirect(array('/' . $this->event->eventType->class_name . '/default/pdfPrint/' . $id));
    }

    public function initActionView()
    {
        parent::initActionView();
        $this->setTitle($this->getManager()->getTitle($this->event));
    }

    /**
     * @return array
     */
    protected function getEventElements()
    {
        if ($this->event && !$this->event->isNewRecord) {
            $for_edit = in_array(strtolower($this->action->id), array('create', 'update'));
            $elements = $this->getManager()->getEventElements($this->event, $for_edit);
        } else {
            $elements = $this->event_type->getDefaultElements();
        }

        $final_elements = array();
        foreach ($elements as $el) {
            $cls = get_class($el);
            if (!$this->checkClinicalEditAccess() && $cls == 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo') {
                if ($el->isNewRecord) {
                    // implies no values have been recorded yet for this element
                    continue;
                }
            }
            if (!$this->checkClericalEditAccess() && $cls == 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo') {
                if ($el->isNewRecord) {
                    continue;
                }
            }

            $final_elements[] = $el;
        }
        return $final_elements;
    }

    /**
     * Override to support the fact that users might not have permission to edit specific event elements.
     *
     * @param \ElementType $element_type
     * @param $data
     * @return array
     * @throws \Exception
     */
    protected function getElementsForElementType(\ElementType $element_type, $data)
    {
        $cls = $element_type->class_name;

        if ($cls == 'OEModule\OphCoCvi\models\Element_OphCoCvi_EventInfo') {
            if ($this->event->isNewRecord) {
                return array(new $cls);
            } else {
                return array($this->getManager()->getEventInfoElementForEvent($this->event));
            }
        }

        // because form elements won't be submitted when editing without this access, we need to return the current
        // event element if it exists
        if (!$this->checkClinicalEditAccess() && $cls == 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo') {
            $el = $this->event->isNewRecord ? null : $this->getManager()->getClinicalElementForEvent($this->event);
            return (!is_null($el)) ? array($el) : null;
        }

        if (!$this->checkClericalEditAccess() && $cls == 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo') {
            $el  = $this->event->isNewRecord ? null : $this->getManager()->getClericalElementForEvent($this->event);
            return (!is_null($el)) ? array($el) : null;
        }

        return parent::getElementsForElementType($element_type, $data);

    }

    /**
     * We set the validation scenario for the models based on whether the user is saving as draft or performing a full save
     *
     * @TODO extend this behaviour so that user can specify they are only interested in validating a specific section.
     * @param $element
     */
    protected function setValidationScenarioForElement($element)
    {
        if ($this->request->getPost('save', null)) {
            // form has been submitted using the save button, so full validation rules should be applied to the elements
            // TODO: validation for signature(s)
            switch (get_class($element)) {
                case 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo':
                    if ($this->checkClinicalEditAccess()) {
                        $element->setScenario('finalise');
                    }
                    break;
                case 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo':
                    if ($this->checkClericalEditAccess()) {
                        $element->setScenario('finalise');
                    }
                    break;
            }
        }
    }

    /**
     * Use the manager status for the event info text.
     *
     * @throws \Exception
     */
    protected function updateEventInfo()
    {
        $status = $this->getManager()->calculateStatus($this->event);
        $this->event->info = $this->getManager()->getStatusText($status);
        $this->event->save();
    }

    /**
     * Element based name and value pair.
     *
     * @param $id
     */
    public function getStructuredDataForPrintPDF($id)
    {
        $data = array();
        foreach ($this->open_elements as $element) {
            if (method_exists($element, "getStructuredDataForPrint")) {
                $data = array_merge($data, $element->getStructuredDataForPrint());
            }
        }
        // TODO: we need to match the keys here!
        // we also need a method to generate the data structure with the ODTDataHandler!
        $data["patientName"] = $this->patient->getFullName();
        // TODO: do we have other names for patient?
        $data["otherNames"] = '';
        $data["patientDateOfBirth"] = $this->patient->dob;
        $data["nhsNumber"] = $this->patient->getNhsnum();
        $data["gpName"] = $this->patient->gp->getFullName();
        //$data["gpAddress"] = $this->patient->gp->contact->address->postcode."\n".$this->patient->gp->contact->address->address1;
        $data["gpAddress"] = '';
        $data["gpTel"] = '';
        $data["patientAddress"] = $this->patient->getSummaryAddress();
        $data["patientEmail"] = ''; // TODO: we need a get email address function
        $data["patientTel"] = $this->patient->getPrimary_phone();
        $data["signatureName"] = $this->patient->getFullName();
        $data["signatureDate"] = date("d/m/Y");

        $genderData = (strtolower($this->patient->getGenderString()) == 'male') ? array('', 'X', '', '') : array(
            '',
            '',
            '',
            'X'
        );
        $dob = ($this->patient->dob) ? $this->patient->NHSDate('dob') : '';
        $yearHeader = !empty($dob) ? array_merge(array(''), str_split(date('Y', strtotime($dob)))) : array(
            '',
            '',
            '',
            '',
            ''
        );
        $postCodeHeader = array('', '', '', '', '');
        $spaceHolder = array('');
        $data["genderTable"] = array(
            0 => array_merge($genderData, $spaceHolder, $yearHeader, $spaceHolder, $postCodeHeader)
        );

        return $data;
    }

    /**
     * @param $id
     */
    public function actionPDFPrint($id)
    {
        if (!$event = \Event::model()->findByPk($id)) {
            throw new Exception("Event not found: $id");
        }
        
        $event->lock();
        $this->printInit($id);
        
        $signatureElement = $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_ConsentSignature');
        //  we need to check if we already have a signature file linked
        if (!$signatureElement->checkSignature()) {
            // we check if the signature is exists on the portal
            $signature = $signatureElement->loadSignatureFromPortal();
        } else {
            // we get the stored signature and creates a GD object from the data
            $signature = imagecreatefromstring($signatureElement->getDecryptedSignature());
        }

        //views/odtTemplates/cviTemplate.odt)
        $inputFile = 'cviTemplate.odt';
        $printHelper = new ODTTemplateManager( 
                $inputFile , 
                realpath(__DIR__ . '/..').'/views/odtTemplate', 
                \Yii::app()->basePath.'/runtime/cache/cvi/',
                'CVICert_'.\Yii::app()->user->id.'_'.rand().'.odt'
        );
        
      
       
        $DH = new ODTDataHandler();
        $DH -> setTableAndSimpleTextDataFromArray( $this->getStructuredDataForPrintPDF($id) );
        
        $tables = $DH -> gettables();
       
        foreach($tables as $oneTable){
            $name = $oneTable['name'];
            $data = $DH->generateSimpleTableHashData($oneTable);
            $printHelper->fillTableByName($name, $data, 'name');
        }
       
    //******* TEST DATAS!!
       
        $data = array( 
            array('','','','','','','','','','','Y'),
            array('','','','','','','','','','','Y'),
            array('','','','','','','','','','','N'),
            array('','','','','','','','','','','Y'),
            array('','','','','','','','','','','N'),
            array('','','','','','','','','','','N'),
            array('','','','','','','','','','','N'),
            array('','','','','','','','','','','N'),
            array('','','','','','','','','','','N'),
        );        
        $printHelper->fillTableByName( 'patientFactors' , $data, 'name' );
        
    //******* TEST DATA END!!
        $texts = $DH -> getSimpleTexts();
        $printHelper->exchangeAllStringValuesByStyleName( $texts );
       
        //$printHelper->exchangeStringValues( $this->getStructuredDataForPrintPDF($id) );
        
        // TODO: we need to check which function to call
        $printHelper->changeImageFromGDObject('signatureImagePatient', $signature);
        $printHelper->saveContentXML();
        $printHelper->generatePDF();
        
        //Print only the first page of the pdf
        if(isset($_GET['firstPage']) && $_GET['firstPage'] == 1 ){
            $printHelper->generatePDFPageN();
        } 
        
        $printHelper->getPDF();
        
        $event->unlock();

    }
}
