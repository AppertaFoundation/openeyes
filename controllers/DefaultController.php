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

use OEModule\OphCoCvi\components\optomPortalConnection;
use OEModule\OphCoCvi\models;
use OEModule\OphCoCvi\components\OphCoCvi_Manager;
use \OEModule\OphCoCvi\components\ODTTemplateManager;
use \OEModule\OphCoCvi\components\SignatureQRCodeGenerator;


class DefaultController extends \BaseEventTypeController
{
    public $event_prompt;
    public $cvi_limit = 2;
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
            $cvi_events = \Yii::app()->moduleAPI->get('OphCoCvi');
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
     * @return boolean
     */
    public function checkClericalEditAccess()
    {
        if ($this->checkAccess('admin')) {
            return true;
        }

        return $this->checkAccess('OprnEditClericalCvi', $this->getApp()->user->id);
    }

    /**
     * @return boolean
     */
    public function checkClinicalEditAccess()
    {
        return $this->checkAccess('OprnEditClinicalCvi', $this->getApp()->user->id);
    }


    /**
     * @param models\Element_OphCoCvi_ClinicalInfo $element
     * @param $action
     */
    protected function setElementDefaultOptions_Element_OphCoCvi_ClinicalInfo(
        models\Element_OphCoCvi_ClinicalInfo $element,
        $action
    )
    {
        // only populate values into the new element if a clinical user
        if ($action == 'create' && $this->checkClinicalEditAccess()) {
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
     * Generate a list of all the CVI events for clerical use.
     */
    public function actionList()
    {
        $this->layout = '//layouts/main';
        $this->renderPatientPanel = false;

        $dp = $this->getManager()->getListDataProvider();

        $this->render('list', array('dp' => $dp));
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
        if (!$this->checkClinicalEditAccess() && $cls == 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo') {
            if ($this->event->isNewRecord) {
                return array(new $cls);
            }
            else {
                return array($this->getManager()->getClinicalElementForEvent($this->event));
            }
        }

        if (!$this->checkClericalEditAccess() && $cls == 'OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo') {
            if ($this->event->isNewRecord) {
                return array(new $cls);
            }
            else {
                return array($this->getManager()->getClericalElementForEvent($this->event));
            }
        }

        return parent::getElementsForElementType($element_type, $data);

    }

    /**
     * Element based name and value pair
     *@param $id
     */
    public function getStructuredDataForPrintPDF($id) {
        $data = array();
        foreach($this->open_elements as $element) {
            if(method_exists($element, "getStructuredDataForPrint")) {
                $data = array_merge($data, $element->getStructuredDataForPrint());
            }
        }
        // TODO: we need to match the keys here!
        // we also need a method to generate the data structure with the ODTDataHandler!
        $data["signatureName"] = $this->patient->getFullName();
        $data["signatureDate"] = date("d/m/Y");

        $genderData = (strtolower($this->patient->getGenderString()) == 'male') ? array('','X','','') : array('','','','X');
        $dob = ($this->patient->dob) ? $this->patient->NHSDate('dob') : '';
        $yearHeader = !empty($dob) ? array_merge(array(''),str_split(date('Y', strtotime($dob)))) : array('','','','','');
        $postCodeHeader = array('','','','','');
        $data["genderTable"] = array(0=> array_merge($genderData, $yearHeader, $postCodeHeader ));

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

        $portalConnection = new optomPortalConnection();
        print_r($portalConnection->signatureSearch());die;

        //  we need to check if we already have a signature file linked
        if(!$this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_ConsentSignature')->checkSignature()){
            $QRContent = "@code:".\Yii::app()->moduleAPI->get('OphCoCvi')->getUniqueCodeForCviEvent($event)."@key:".md5("here comes the key");

            $QRHelper = new SignatureQRCodeGenerator();
            $signature = $QRHelper->generateQRSignatureBox($QRContent);
        }else{
            // we get the stored signature
            $signature =  $this->getOpenElementByClassName('OEModule_OphCoCvi_models_Element_OphCoCvi_ConsentSignature')->signature_file;
        }


        // TODO: need to find a place for the template files! (eg. views/odtTemplates) ?
        $inputFile = 'example_certificate_5.odt';
        $printHelper = new ODTTemplateManager( $inputFile , realpath(__DIR__ . '/..').'/files', 'CVICert_'.\Yii::app()->user->id.'_'.rand().'.odt');

        $data = array( 
            array('','','','X','','','1','9','7','5','','','6','7','2','6') 
        );
        $printHelper->fillTableByStyleName( $styleName='sexTable' , $data );


        $data = array( 
            array('','','','','','','','','','','Y'),
            array('','','','','','','','','','','Y'),
            array('','','','','','','','','','','N'),
            array('','','','','','','','','','','Y'),
            array('','','','','','','','','','','N'),
        );        
        $printHelper->fillTableByStyleName( $styleName='otherRelevantFactorsTable' , $data );
        


        //$printHelper->exchangeStringValues( $this->getStructuredDataForPrintPDF($id) );
        
        $printHelper->exchangeAllStringValuesByStyleName( $this->getStructuredDataForPrintPDF($id) );
        //$printHelper->exchangeAllStringValuesByNodes( $this->getStructuredDataForPrintPDF($id) );
        // TODO: we need to check which function to call
        $printHelper->changeImageFromGDObject('signature1', $signature);
        $printHelper->saveContentXML();
        $printHelper->generatePDF();
        $printHelper->getPDF();
        $event->unlock();

        /*$pdf = $event->getPDF($this->pdf_print_suffix);

        header('Content-Type: application/pdf');
        header('Content-Length: '.filesize($pdf));

        readfile($pdf);
        @unlink($pdf);*/

    }

}
