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


namespace OEModule\OphCoCvi\components;

use OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo;
use OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo;
use OEModule\OphCoCvi\models\Element_OphCoCvi_EventInfo;
use OEModule\OphCoCvi\models\Element_OphCoCvi_Demographics_V1;
use OEModule\OphCoCvi\models\Element_OphCoCvi_EventInfo_V1;
use OEModule\OphCoCvi\models\Element_OphCoCvi_PatientSignature;
use mikehaertl\pdftk\Pdf;
use OEModule\OphCoCvi\models\SignatureInterface;
use OEModule\OphCoMessaging\components\MessageCreator;
use OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType;

require_once str_replace('index.php', 'vendor/setasign/fpdi/src/PdfParser/PdfParser.php', \Yii::app()->getRequest()->getScriptFile());
/**
 * Class OphCoCvi_Manager
 *
 * @package OEModule\OphCoCvi\components
 */
class OphCoCvi_Manager extends \CComponent
{
    public static $CLINICAL_COMPLETE = 1;
    public static $CLERICAL_COMPLETE = 2;
    public static $DEMOGRAPHICS_COMPLETE = 4;
    public static $ISSUED = 8;
    public static $CONSENTED = 16;
    public static $CONSULTANT_SIGNED = 32;
    
    public static $SIGHT_IMPAIRED = 'SI';
    public static $SEVERELY_SIGHT_IMPAIRED = 'SSI';
    private $input_template_file = 'cviTemplate.odt';
    
    public $outDir;
    private $cviTemplate;
    public $patientSignatureImage;
    public $consultantSignatureImage;
    private $pdfOutput;
    
    private $gpConsentImage;
    private $laConsentImage;
    private $rcConsentImage;
    private $diagnosisImage;
    private $signedByImage;
    
    private $centralVisualPathwayPromblems;
    
    private $anophtalmosMicrophthalmos = null;
    private $disorganisedGlobePhthisis = null;
    private $primaryCongenitalInfantileGlaucoma = null;
    private $gray_rectangle;
    private $white_rectangle;

    public $is_considered_blind = "";
   
    /**
     * @param $status
     * @return string
     */
    public function getStatusText($status)
    {
        $isConsideredBlind = "";
        if($this->is_considered_blind !== ""){
            $isConsideredBlind = ' - '.$this->is_considered_blind;
        }
        
        if ($status & self::$ISSUED) {
            return 'Issued'.$isConsideredBlind;
        }
        $map = array(
            'Clinical' => self::$CLINICAL_COMPLETE,
            'Clerical' => self::$CLERICAL_COMPLETE,
            'Demographics' => self::$DEMOGRAPHICS_COMPLETE,
            'Consent signature' => self::$CONSENTED,
            'Consultant signature' => self::$CONSULTANT_SIGNED,
        );

        $result = array();
       
        foreach ($map as $label => $flag) {
            if (($status & $flag) != $flag) {
                $result[] = $label;
            }
        }
        
        if (count($result) === count($map)) {
            return 'Incomplete'.$isConsideredBlind;
        } elseif (count($result) === 0) {
            return 'Complete'.$isConsideredBlind;
        } else {
            return 'Incomplete/Missing: ' . implode(', ', $result) .$isConsideredBlind;
        }
    }

    protected $yii;

    /**
     * @var \EventType
     */
    protected $event_type;

    /**
     * OphCoCvi_Manager constructor.
     *
     * @param \CApplication|null $yii
     * @param \EventType|null    $event_type
     */
    public function __construct(\CApplication $yii = null, \EventType $event_type = null)
    {
        if (is_null($yii)) {
            $yii = \Yii::app();
        }

        if (is_null($event_type)) {
            $event_type = $this->determineEventType();
        }
        $this->event_type = $event_type;

        $this->yii = $yii;
        $this->outDir = $this->yii->basePath. '/runtime/cache/cvi/';
        if(!is_dir($this->outDir)){
            mkdir($this->outDir, 0777, true);
        }
        $this->cviTemplate = realpath(__DIR__ . '/..') . '/views/odtTemplate/cviTemplate.pdf';
        $this->gpConsentImage = realpath(__DIR__ . '/..') . '/assets/img/gp_consent.png';
        $this->laConsentImage = realpath(__DIR__ . '/..') . '/assets/img/la_consent.png';
        $this->rcConsentImage = realpath(__DIR__ . '/..') . '/assets/img/royal_college_consent.png';
        
        $this->diagnosisImage = realpath(__DIR__ . '/..') . '/assets/img/diagnosis_image.png';
        $this->gray_rectangle = realpath(__DIR__ . '/..') . '/assets/img/gray_rectangle.png';
        $this->white_rectangle = realpath(__DIR__ . '/..') . '/assets/img/white_rectangle.png';
    }

    /**
     * Returns the non-namespaced module class of the module API Instance
     *
     * @return mixed
     */
    protected function getModuleClass()
    {
        $namespace_pieces = explode("\\", __NAMESPACE__);

        return $namespace_pieces[1];
    }

    /**
     * @return \EventType
     * @throws \Exception
     */
    protected function determineEventType()
    {
        $module_class = $this->getModuleClass();

        if (!$event_type = \EventType::model()->find('class_name=?', array($module_class))) {
            throw new \Exception("Module is not migrated: $module_class");
        }

        return $event_type;
    }

    /**
     * Wrapper for starting a transaction.
     *
     * @return \CDbTransaction|null
     */
    protected function startTransaction()
    {
        return $this->yii->db->beginInternalTransaction();
    }

    /**
     * @param \Patient $patient
     * @return \Event[]
     */
    public function getEventsForPatient(\Patient $patient)
    {
        return \Event::model()->getEventsOfTypeForPatient($this->event_type, $patient);
    }

    protected $info_el_for_events = array();

    /**
     * @param $event
     * @param $element_class
     * @return \CActiveRecord|null
     */
    protected function getElementForEvent($event, $element_class)
    {
        $version = '_V1';
        $core_class = 'Element_OphCoCvi_EventInfo'.$version;
        $namespaced_class = '\\OEModule\OphCoCvi\\models\\' . $core_class;

        $cls_rel_map = array(
            'Element_OphCoCvi_ClinicalInfo'.$version => 'clinical_element',
            'Element_OphCoCvi_ClericalInfo'.$version => 'clerical_element',
            'Element_OphCoCvi_Demographics'.$version => 'demographics_element',
            'Element_OphCoCvi_ConsultantSignature'  => 'consultant_element'
        );
        
        if (!isset($this->info_el_for_events[$event->id])) {
            $this->info_el_for_events[$event->id] = $namespaced_class::model()->with(array_values($cls_rel_map))->findByAttributes(array('event_id' => $event->id));
        }
        
        if (array_key_exists($element_class, $cls_rel_map)) {
            return $this->info_el_for_events[$event->id]->{$cls_rel_map[$element_class]};
        } elseif ($element_class == $core_class) {
            return $this->info_el_for_events[$event->id];
        }
    }

    /**
     * Convenience wrapper to clear out element data when put into specific states that we don't want to keep
     *
     * @TODO this might not be necessary if there's a sensible way to clear out the validation state of an ActiveRecord
     * @param \Event $event
     */
    protected function resetElementStore(\Event $event = null)
    {
        if ($event) {
            unset($this->info_el_for_events[$event->id]);
        } else {
            $this->info_el_for_events = array();
        }
    }

    /**
     * @param \Event $event
     * @return \Patient
     */
    protected function getPatientForEvent(\Event $event)
    {
        return $event->episode->patient;
    }

    /**
     * Wrapper to insert missing elements for a CVI event if they haven't been
     * created (due to access restrictions)
     *
     * NB The inserted elements may be removed in the view context if the user still
     * doesn't have the right to manage the data for that specific element.
     *
     * @param \Event $event
     * @param bool   $for_editing
     * @return array|\BaseEventTypeElement[]
     */
    public function getEventElements(\Event $event, $for_editing = false)
    {
        if (!$for_editing) {
            return $event->getElements();
        } else {
            $default = $event->eventType->getDefaultElements();
            $current = $event->getElements();
            if (count($current) == $default) {
                // assume a match implies all the elements are already recorded for the event.
                return $current;
            }

            $editable = array();
            foreach ($default as $el) {
                // check there's something in the current list as might be the last default elements that are not defined
                if (isset($current[0]) && get_class($el) == get_class($current[0])) {
                    // the order should be consistent across default and current.
                    $editable[] = array_shift($current);
                } else {
                    $editable[] = $el;
                }
            }

            return $editable;
        }
    }

    /**
     * @param \Event $event
     * @return null|Element_OphCoCvi_EventInfo
     */
    public function getEventInfoElementForEvent(\Event $event)
    {
        return $this->getElementForEvent($event, 'Element_OphCoCvi_EventInfo_V1');
    }

    /**
     * @param \Event $event
     * @return null|Element_OphCoCvi_ClinicalInfo
     */
    public function getClinicalElementForEvent(\Event $event)
    {
        return $this->getElementForEvent($event, 'Element_OphCoCvi_ClinicalInfo_V1');
    }

    /**
     * @param \Event $event
     * @return null|Element_OphCoCvi_ClericalInfo
     */
    public function getClericalElementForEvent(\Event $event)
    {
        return $this->getElementForEvent($event, 'Element_OphCoCvi_ClericalInfo_V1');
    }

    /**
     * @param \Event $event
     * @return null|SignatureInterface
     */
    public function getConsentSignatureElementForEvent(\Event $event)
    {
        return $this->getElementForEvent($event, "Element_OphCoCvi_PatientSignature");
    }

    /**
     * @param \Event $event
     * @return Element_OphCoCvi_Demographics|null
     */
    public function getDemographicsElementForEvent(\Event $event)
    {
        return $this->getElementForEvent($event, 'Element_OphCoCvi_Demographics_V1');
    }

    /**
     * Generate the text display of the status of the CVI
     *
     * @param Element_OphCoCvi_ClinicalInfo $clinical
     * @param Element_OphCoCvi_EventInfo    $info
     * @return string
     */
    protected function getDisplayStatus($clinical = null, $info)
    {
        return ($clinical ? $clinical->getDisplayStatus() : "Not Assessed") . ' (' . $info->getIssueStatusForDisplay() . ')';
    }

    /**
     * @param \Event $event
     * @return string
     */
    public function getDisplayStatusForEvent(\Event $event)
    {
        $clinical = $this->getClinicalElementForEvent($event);
        $info = $this->getEventInfoElementForEvent($event);

        return $this->getDisplayStatus($clinical, $info);
    }

    /**
     * @param Element_OphCoCvi_EventInfo $element
     * @return string
     */
    public function getDisplayStatusFromEventInfo(Element_OphCoCvi_EventInfo $element)
    {
        return $this->getDisplayStatus($element->clinical_element, $element);
    }

    /**
     * @param \Event $event
     * @return string|null
     */
    public function getDisplayStatusDateForEvent(\Event $event)
    {
        // this used to be the examination date ... and in the future we could perhaps pull out an issue date
        return $event->event_date;
    }

    /**
     * @param \Event $event
     * @return string
     */
    public function getEventViewUri(\Event $event)
    {
        return $this->yii->createUrl($event->eventType->class_name . '/default/view/' . $event->id);
    }

    /**
     * @param \Event $event
     * @return string
     */
    public function getTitle(\Event $event)
    {
        $title = $event->eventType->name;

        if ($event->info) {
            // this should always be set.
            $title .= ' - ' . $event->info;
        }

        return $title;
    }

    /**
     * @param Element_OphCoCvi_EventInfo $event_info
     * @return \User|null
     */
    public function getClinicalConsultant(Element_OphCoCvi_EventInfo $event_info)
    {
        /**
         * @var Element_OphCoCvi_ClinicalInfo
         */
        if ($clinical = $event_info->clinical_element) {
            return $clinical->consultant;
        }

        return null;
    }

    public function getConsultantSignedBy(Element_OphCoCvi_EventInfo $event_info)
    {
        /**
         * @var Element_OphCoCvi_ClinicalInfo
         */

        if ($consultant = $event_info->consultant_element) {
            return $consultant;
        }

        return null;
    }

    /**
     * @param \Event $event
     * @return bool
     */
    public function canIssueCvi(\Event $event)
    {
        /*
         * TODO:: The 'is_draft' can only be true by green issue button, so we don't understand this part
        if ($info = $this->getEventInfoElementForEvent($event)) {
            if (!$info->is_draft) {
                return false;
            }
        } else {
            return false;
        }
        */
        
        if ($clinical = $this->getClinicalElementForEvent($event)) {
            $clinical->setScenario('finalise');

            if (!$clinical->validate()) {
                return false;
            }
            
        } else {
            return false;
        }
        
        if($consultant_signature = $this->getElementForEvent($event, "Element_OphCoCvi_ConsultantSignature")) {
            /** @var SignatureInterface $consultant_signature */
            if(!$consultant_signature->checkSignature()) {
                return false;
            }
        } else {
            return false;
        }

        if ($clerical = $this->getClericalElementForEvent($event)) {
            $clerical->setScenario('finalise');

            if (!$clerical->validate()) {
                return false;
            }
        } else {
            return false;
        }

        if ($demographics = $this->getDemographicsElementForEvent($event)) {
            $demographics->setScenario('finalise');
            if (!$demographics->validate()) {
                return false;
            }
        } else {
            return false;
        }

        if ($signature = $this->getConsentSignatureElementForEvent($event)) {
            if (!$signature->checkSignature()) {
                return false;
            }
            if ( is_null($signature->consented_to_gp) || is_null($signature->consented_to_la) && is_null($signature->consented_to_rcop)) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * @param \Event $event
     * @return bool
     */
    public function canEditEvent(\Event $event)
    {
        if ($info_element = $this->getEventInfoElementForEvent($event)) {
            return $info_element->is_draft;
        }

        return false;
    }

    /**
     * @param \Patient $patient
     * @return bool
     */
    public function canCreateEventForPatient(\Patient $patient)
    {
        foreach ($this->getEventsForPatient($patient) as $e) {
            if (!$this->isIssued($e)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Element based name and value pair.
     *
     * @param \Event $event
     * @return array
     */
    protected function getStructuredDataForPrintPDF($event)
    {
        $data = array();
        $elements_array = array('Clinical', 'Clerical', 'ConsentSignature', 'Demographics');

        foreach ($elements_array as $el_name) {
            $element = $this->{"get{$el_name}ElementForEvent"}($event);
            if (method_exists($element, 'getStructuredDataForPrint')) {
                $data = array_merge($data, $element->getStructuredDataForPrint());
            }
        }

        $institutionInfo = \Institution::model()->getCurrent();
        $address = $institutionInfo->name . '\n' . \Institution::model()->getCurrent()->getLetterAddress(array('include_name' => false, 'delimiter' => '\n'));
        $data['hospitalAddress'] = \Helper::lineLimit($address, 2, 1, '\n');
        $data['hospitalAddressMultiline'] = \Helper::lineLimit($address, 4, 1, '\n');
        $data['hospitalNumber'] = $event->episode->patient->hos_num;

        return $data;
    }

    /**
     * Prepare the Certificate template with the data available from the given event.
     *
     * @param \Event  $event
     * @param boolean $ignore_portal - if true, will force the signature box to be rendered rather than checking the portal
     * @return \ODTTemplateManager
     * @throws \Exception
     */
    protected function populateCviCertificate(\Event $event, $ignore_portal = false)
    {
        $signature_element = $this->getConsentSignatureElementForEvent($event);
        
        //  we need to check if we already have a signature file linked
        if (!$signature_element->checkSignature()) {
            //TODO: restructure or rename, as this process is basically also going to generate
            //TODO: the QR code signature placeholder when its not yet been captured.
            // we check if the signature is exists on the portal
            $signature = $ignore_portal ? $signature_element->getSignatureBox() : $signature_element->loadSignatureFromPortal();
        } else {
            // TODO: this should be checked before, when we retrieve the patient signature!!!
            // we get the stored signature and creates a GD object from the data
            if ($signature_element->getDecryptedSignature()) {
                $signature = imagecreatefromstring($signature_element->getDecryptedSignature());
            } else {
                $signature = imagecreatetruecolor(1, 1);
            }

        }


        // TODO: need to configure this more cleanly
        $print_helper = new \ODTTemplateManager(
            $this->input_template_file,
            realpath(__DIR__ . '/..') . '/views/odtTemplate',
            $this->yii->basePath . '/runtime/cache/cvi/',
            'CVICert_' . $event->id . '_' . mt_rand() . '.odt'
        );

        $data_handler = new \ODTDataHandler();
        $structured_data = $this->getStructuredDataForPrintPDF($event);
        $data_handler->setTableAndSimpleTextDataFromArray($structured_data);

        $tables = $data_handler->getTables();

        foreach ($tables as $one_table) {
            $name = $one_table['name'];
            $data = $data_handler->generateSimpleTableHashData($one_table);
            $print_helper->fillTableByName($name, $data, 'name');
        }

        $texts = $data_handler->getSimpleTexts();

        $print_helper->exchangeAllStringValuesByStyleName($texts);

        //$print_helper->exchangeStringValues( $this->getStructuredDataForPrintPDF($id) );

        // TODO: This should be handled more cleanly for the image manipulation
        $print_helper->changeImageFromGDObject('signatureImagePatient', $signature);
        if (array_key_exists('signatureImageConsultant', $structured_data)) {
            $print_helper->changeImageFromGDObject('signatureImageConsultant', $structured_data['signatureImageConsultant']);
        }
        $print_helper->saveContentXML();
        $print_helper->generatePDF();

        return $print_helper;
    }

    /**
     * Create the CVI Certificate and store it as a ProtectedFile.
     *
     * @param \Event $event
     * @return \ProtectedFile
     */
    protected function generateCviCertificate(\Event $event)
    {
        //$document = $this->populateCviCertificate($event);
        if($this->fillPDFForm( $event )){
            $storedPDF = $this->getConsentPDF();
        }
        return $storedPDF;
    }

    /**
     * Generate the CVI Consent Form for the patient to sign.
     *
     * @param \Event $event
     * @return \ODTTemplateManager
     */
    public function generateConsentForm(\Event $event)
    {
        $this->input_template_file = "signatureTemplate.odt";
        $document = $this->populateCviCertificate($event, true);
        $document->generatePDFPageN();

        return $document;
    }

    /**
     * Issue the CVI for the given event (recording it as an action performed by the given user id).
     *
     * @param \Event $event
     * @param        $user_id
     * @return bool
     */
    public function issueCvi(\Event $event, $user_id)
    {
        // begin transaction
        $transaction = $this->startTransaction();

        try {
            $event->lock();

            $cvi_certificate = $this->generateCviCertificate($event);
           
            // set the status of the event to complete and assign the PDF to the event
            $info_element = $this->getEventInfoElementForEvent($event);
            $info_element->is_draft = false;
            $info_element->generated_document_id = $cvi_certificate->id;
            $info_element->save();

            $this->updateEventInfo($event);

            $event->info = $this->getStatusText(self::$ISSUED);
            $event->save();

            $event->audit('event', 'cvi-issued', null, 'CVI Issued', array('user_id' => $user_id));

            /** @var Element_OphCoCvi_PatientSignature $consent_element */
            $consent_element = $this->getConsentSignatureElementForEvent($event);

            $info_element->gp_delivery = $gp_delivery = (int)($consent_element->consented_to_gp && $consent_element::isDocmanEnabled());
            $info_element->la_delivery = $la_delivery = (int)($consent_element->consented_to_la && $consent_element::isLADeliveryEnabled());
            $info_element->rco_delivery = $rco_delivery = (int)($consent_element->consented_to_rcop && $consent_element::isRCOPDeliveryEnabled());
            $info_element->gp_delivery_status = $gp_delivery === 1 ? Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_PENDING : null;
            $info_element->la_delivery_status = $la_delivery === 1 ? Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_PENDING : null;
            $info_element->rco_delivery_status = $rco_delivery === 1 ? Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_PENDING : null;
            $info_element->save();

            $transaction->commit();

            $event->unlock();

            return true;
        } catch (\Exception $e) {
            \OELog::log($e->getMessage()." ".$e->getFile().", Line: ".$e->getLine()."\n".$e->getTraceAsString());
            $transaction->rollback();
        }

        return false;

    }

    /**
     * @param \Event $event
     * @return bool
     */
    public function isIssued(\Event $event)
    {
        if ($info_element = $this->getEventInfoElementForEvent($event)) {
            return !$info_element->is_draft;
        } else {
            return false;
        }
    }

    /**
     * @param \Event $event
     * @return mixed
     */
    public function calculateStatus(\Event $event)
    {
        $status = 0;
        // need to reset to ensure we are getting the correct values for the elements
        // after any edits that have taken place (edits taking place in the controller
        // operate on different instances of the event elements from those cached in the manager)
        // TODO: Would be good to fix the Controller so this was no longer the case.
        $this->resetElementStore($event);

        if ($this->isIssued($event)) {
            $status |= self::$ISSUED;
        }

        if ($clerical = $this->getClericalElementForEvent($event)) {
            $clerical->setScenario('finalise');
            if ($clerical->validate()) {
                $status |= self::$CLERICAL_COMPLETE;
            }
        }

        if ($clinical = $this->getClinicalElementForEvent($event)) {
            $clinical->setScenario('finalise');

            if ($clinical->validate()) {
                $status |= self::$CLINICAL_COMPLETE;
            }
        }

        if ($esign_element = $event->getElementByClass(\Element_OphCoCvi_Esign::class)) {
            /** @var \Element_OphCoCvi_Esign $esign_element */
            foreach ($esign_element->signatures as $signature) {
                if ((int)$signature->type === \BaseSignature::TYPE_PATIENT && $signature->isSigned()) {
                    $status |= self::$CONSENTED;
                } elseif ((int)$signature->type === \BaseSignature::TYPE_LOGGEDIN_USER && $signature->isSigned()) {
                    $status |= self::$CONSULTANT_SIGNED;
                }
            }
        }

        if ($demographics = $this->getDemographicsElementForEvent($event)) {
            $demographics->setScenario('finalise');
           
            if ($demographics->validate()) {
                $status |= self::$DEMOGRAPHICS_COMPLETE;
            }
        }

        $this->resetElementStore($event);

        return $status;
    }


    /**
     * @param \CDbCriteria $criteria
     * @param array        $filter
     */
    private function handleDateRangeFilter(\CDbCriteria $criteria, $filter = array())
    {
        $from = null;
        if (isset($filter['date_from'])) {
            $from = \Helper::convertNHS2MySQL($filter['date_from']);
        }
        $to = null;
        if (isset($filter['date_to'])) {
            $to = \Helper::convertNHS2MySQL($filter['date_to']);
        }
        if ($from && $to) {
            if ($from > $to) {
                $criteria->addBetweenCondition('event.event_date', $to, $from);
            } else {
                $criteria->addBetweenCondition('event.event_date', $from, $to);
            }
        } elseif ($from) {
            $criteria->addCondition('event.event_date >= :from');
            $criteria->params[':from'] = $from;
        } elseif ($to) {
            $criteria->addCondition('event.event_date <= :to');
            $criteria->params[':to'] = $to;
        }
    }

    /**
     * @param \CDbCriteria $criteria
     * @param array        $filter
     */
    private function handleSubspecialtyListFilter(\CDbCriteria $criteria, $filter = array())
    {
        if (array_key_exists('subspecialty_id', $filter) && $filter['subspecialty_id'] !== '') {
            $criteria->addCondition('subspecialty.id = :subspecialty_id');
            $criteria->params[':subspecialty_id'] = $filter['subspecialty_id'];
        }
    }

    /**
     * @param \CDbCriteria $criteria
     * @param array        $filter
     */
    private function handleSiteListFilter(\CDbCriteria $criteria, $filter = array())
    {
        if (array_key_exists('site_id', $filter) && $filter['site_id'] !== '') {
            $criteria->addCondition('site_id = :site_id');
            $criteria->params[':site_id'] = $filter['site_id'];
        }
    }

    /**
     * @param \CDbCriteria $criteria
     * @param array        $filter
     */
    private function handleConsultantInChargeListFilter(\CDbCriteria $criteria, $filter = array())
    {
        if (isset($filter['firm_ids']) && strlen(trim($filter['firm_ids']))) {
            $criteria->addInCondition('consultant_in_charge_of_this_cvi_id', explode(',', $filter['firm_ids']));
        }
    }

    /**
     * @param \CDbCriteria $criteria
     * @param array        $filter
     */
    private function handleCreatedByListFilter(\CDbCriteria $criteria, $filter = array())
    {
        if (isset($filter['createdby_ids']) && strlen(trim($filter['createdby_ids']))) {
            $criteria->addInCondition('event.created_user_id', explode(',', $filter['createdby_ids']));
        }
    }


    /**
     * @param \CDbCriteria $criteria
     * @param array        $filter
     */
    private function handleConsultantListFilter(\CDbCriteria $criteria, $filter = array())
    {
        if (isset($filter['consultant_ids']) && strlen(trim($filter['consultant_ids']))) {
            $criteria->addInCondition('consultant_element.signed_by_user_id', explode(',', $filter['consultant_ids']));
        }
    }


    /**
     * @param \CDbCriteria $criteria
     * @param array        $filter
     */
    private function handleIssuedFilter(\CDbCriteria $criteria, $filter = array())
    {
        //WTF???
        if ((!array_key_exists('issue_complete', $filter) || (isset($filter['issue_complete']) && (bool)$filter['issue_complete']))
            AND (!array_key_exists('issue_incomplete', $filter) || (isset($filter['issue_incomplete']) && (bool)$filter['issue_incomplete']))
            AND (isset($filter['show_issued']) && (bool)$filter['show_issued'])) {
                $criteria->addCondition('t.is_draft = false OR event.info LIKE "Complete%" OR event.info LIKE "Incomplete%"');
        } elseif ((!array_key_exists('issue_complete', $filter) || (isset($filter['issue_complete']) && (bool)$filter['issue_complete']))
        AND (!array_key_exists('issue_incomplete', $filter) || (isset($filter['issue_incomplete']) && (bool)$filter['issue_incomplete']))){
            $criteria->addCondition('event.info LIKE "Complete%" OR event.info LIKE "Incomplete%"');
        }  elseif ((!array_key_exists('issue_complete', $filter) || (isset($filter['issue_complete']) && (bool)$filter['issue_complete']))
            AND (isset($filter['show_issued']) && (bool)$filter['show_issued'])) {
               $criteria->addCondition('t.is_draft = false OR event.info LIKE "Complete%"');
        } elseif ((!array_key_exists('issue_incomplete', $filter) || (isset($filter['issue_incomplete']) && (bool)$filter['issue_incomplete']))
            AND (isset($filter['show_issued']) && (bool)$filter['show_issued'])) {
            $criteria->addCondition('t.is_draft = false OR event.info LIKE "Incomplete%"');
        } elseif ((!array_key_exists('issue_complete', $filter) || (isset($filter['issue_complete']) && (bool)$filter['issue_complete'])))
        {
            $criteria->addCondition('event.info LIKE "Complete%"');
        } elseif ((!array_key_exists('issue_incomplete', $filter) || (isset($filter['issue_incomplete']) && (bool)$filter['issue_incomplete'])))
            {
            $criteria->addCondition('event.info LIKE "Incomplete%"');
        } elseif (isset($filter['show_issued']) && (bool)$filter['show_issued']) {
            $criteria->addCondition('t.is_draft = false');
        }
    }
    
    /**
     * If "Missing Consultant Signature" and "Missing Clerical Part" are checked 
     * @param \CDbCriteria $criteria
     * @param array $filter
     */
    private function handleMissingFilter(\CDbCriteria $criteria, $filter = array())
    {
        if(isset($filter['missing_consultant_signature']) && $filter['missing_consultant_signature'] == "1") {
            $criteria->addCondition(
                "event.info LIKE '%Consultant signature%'"
            );
        }

        if(isset($filter['missing_clerical_part']) && $filter['missing_clerical_part'] == "1") {
            $criteria->addCondition(
                "event.info LIKE '%Clerical%'"
            );
        }
    }

    /**
     * @param array $filter
     * @return \CDbCriteria
     */
    protected function buildFilterCriteria($filter = array())
    {
        $criteria = new \CDbCriteria();
        $this->handleMissingFilter($criteria, $filter);
        $this->handleDateRangeFilter($criteria, $filter);
        $this->handleSubspecialtyListFilter($criteria, $filter);
        $this->handleSiteListFilter($criteria, $filter);
        $this->handleCreatedByListFilter($criteria, $filter);
        $this->handleConsultantListFilter($criteria, $filter);
        $this->handleIssuedFilter($criteria, $filter);
        $this->handleConsultantInChargeListFilter($criteria, $filter);
        return $criteria;
    }

    /**
     * Abstraction of the list provider
     *
     * @param array $filter
     * @return \CActiveDataProvider
     */
    public function getListDataProvider($filter = array(), $pagination = true)
    {
        $model = Element_OphCoCvi_EventInfo::model()->with(
            'site',
            'user',
            'clinical_element',
            'clinical_element.consultant',
            'clerical_element',
            'consultant_element',
            'event',
            'event.episode.patient.contact',
            'event.episode.firm.serviceSubspecialtyAssignment.subspecialty',
            'consultantInChargeOfThisCvi'
        );

        $sort = new \CSort();

        $sort->attributes = array(
            'event_date' => array(
                'asc' => 'event.event_date asc, event.id asc',
                'desc' => 'event.event_date desc, event.id desc',
            ),
            'subspecialty' => array(
                'asc' => 'lower(subspecialty.name) asc, event.id asc',
                'desc' => 'lower(subspecialty.name) desc, event.id desc',
            ),
            'site' => array(
                'asc' => 'lower(site.name) asc, event.id asc',
                'desc' => 'lower(site.name) desc, event.id desc',
            ),
            'patient_name' => array(
                'asc' => 'lower(contact.last_name) asc, lower(contact.first_name) asc',
                'desc' => 'lower(contact.last_name) desc, lower(contact.first_name) desc',
            ),
            'hosnum' => array(
                'asc' => 'patient.hos_num asc, patient.id asc, event.id asc',
                'desc' => 'patient.hos_num desc, patient.id desc, event.id desc',
            ),
            'creator' => array(
                'asc' => 'lower(user.last_name) asc, lower(user.first_name) asc, event.id asc',
                'desc' => 'lower(user.last_name) desc, lower(user.first_name) desc, event.id desc',
            ),
            'consultant' => array(
                'asc' => 'lower(consultant.last_name) asc, lower(consultant.first_name) asc, event.id asc',
                'desc' => 'lower(consultant.last_name) desc, lower(consultant.first_name) desc, event.id desc',
            ),
            'consultant_in_charge_of_this_cvi_id' => array(
                'asc' => 'lower(consultantInChargeOfThisCvi.name) asc, lower(consultantInChargeOfThisCvi.name) asc, event.id asc',
                'desc' => 'lower(consultantInChargeOfThisCvi.name) desc, lower(consultantInChargeOfThisCvi.name) desc, event.id desc',
            ),
            'issue_status' => array('asc' => 'is_draft desc, event.id asc', 'desc' => 'is_draft asc, event.id desc'),
            // no specific issue date field
            // TODO: retrieve the date attribute from the info element class
            'issue_date' => array(
                'asc' => 'is_draft asc, t.last_modified_date asc',
                'desc' => 'is_draft asc, t.last_modified_date desc',
            ),
        );
        $criteria = $this->buildFilterCriteria($filter);
        
        $paginationArr = array();
        if($pagination === false ){
            $paginationArr = array('pagination' => false);
        }
       
        return new \CActiveDataProvider($model, array_merge( 
            array(
                'sort' => $sort,
                'criteria' => $criteria,
            ), 
            $paginationArr)
        );
    }

    /**
     * @param $event
     */
    public function updateEventInfo(\Event $event)
    {
        $status = $this->calculateStatus($event);
        $event->info = $this->getStatusText($status);
        $event->save();
    }

    /**
     * @param        $signatureFile
     * @param \Event $event
     * @throws \Exception
     */
    public function saveUserSignature($signatureFile, \Event $event)
    {
        $portal_connection = new \OptomPortalConnection();

        if ($new_file = $portal_connection->createNewSignatureImage($signatureFile, $event->id)) {
            if ($clinic_element = $this->getClinicalElementForEvent($event)) {
                $clinic_element->consultant_signature_file_id = $new_file->id;
                $clinic_element->consultant_id = \Yii::app()->user->id;
                $clinic_element->save();
            } else {
                throw new \Exception("Could not find clinical element for event " . $event->id);
            }
        } else {
            throw new \Exception("could not create event signature file");
        }

    }

    /**
     * @param \Event $event
     * @param \User  $user
     * @param        $pin
     * @return bool
     */
    public function signCvi(\Event $event, \User $user, $pin)
    {
        if ($user->signature_file_id) {

            $decodedImage = $user->getDecryptedSignature($pin);
            if ($decodedImage) {
                $transaction = $this->startTransaction();
                try {
                    $this->saveUserSignature($decodedImage, $event);
                    $this->updateEventInfo($event);
                    $event->audit('event', 'cvi-consultant-signed', null, 'CVI Consultant Signature added', array('user_id' => $user->id));
                    $transaction->commit();

                    return true;
                } catch (\Exception $e) {
                    \OELog::log($e->getMessage());
                    $transaction->rollback();

                    return false;
                }
            }
        }

        return false;
    }

    /**
     * @param \Event $event
     * @param \User  $user
     * @param        $signature_file_id
     * @return bool
     * @throws \CDbException
     * @throws \Exception
     */
    public function removeConsentSignature(\Event $event, \User $user, $signature_file_id)
    {
        if ($element = $this->getConsentSignatureElementForEvent($event)) {
            if ($element->signature_file_id == $signature_file_id) {
                $transaction = $this->startTransaction();
                try {
                    $element->signature_file_id = null;
                    $element->save();
                    $this->updateEventInfo($event);
                    $event->audit('event', 'cvi-consent-removed', null, 'CVI Consent Signature Removed', array('user_id' => $user->id));
                    $transaction->commit();

                    return true;
                } catch (\Exception $e) {
                    \OELog::log($e->getMessage());
                    $transaction->rollback();

                    return false;
                }
            }
        }

        return false;
    }
    
    /**
     * Include and fill the original empty PDF
     * @param \Event $event
     */
    public function fillPDFForm(\Event $event)
    {
        $rand =  mt_rand();
      
        $this->pdfOutput = $this->outDir . 'CVICert_' . $event->id . '_' . $rand . '.pdf';
   
        if($cviElements = $this->generateCviElementsForPDF($event)){
            $pdf = new Pdf($this->cviTemplate);
            unset( $cviElements['EthnicityForVisualyImpaired']);
            unset( $cviElements['diagnosis_for_visualy_impaired']);

            if(!$pdf->fillForm($cviElements)
                ->needAppearances()
                ->flatten()
                ->saveAs( $this->pdfOutput )) {

                throw new \Exception("Could not save CVI template: ".$pdf->getError());
            }
           
            $fpdf = new \FPDI();

            $pagecount = $fpdf->setSourceFile( $this->pdfOutput );

            for ($page = 1; $page <= $pagecount; $page++) {
                $fpdf->importPage($page);
                $fpdf->AddPage();
                $fpdf->useTemplate($page);

                // Remove page numbers from the entire document
                $rectangle = $page == 2 ? $this->white_rectangle : $this->gray_rectangle;
                $fpdf->Image($rectangle, 97, 291);
                
                if($page == 1){
                    $consultantGDImage = $this->generateGDFromSignature( $cviElements['consultantSignature'] );
                    $this->changeConsultantImageFromGDObject('consultant_signature_'.$rand, $consultantGDImage);
                    $fpdf->Image( $this->consultantSignatureImage , 30, 194, 35, 5);
                }
                
                if(($page == 3) && ($cviElements['patient_type'] == 1)){   
                    
                    if( $this->setDiagnosisUnder18( $cviElements )){
                        $fpdf->Image( $this->centralVisualPathwayPromblems , 34, 24, 78, 9);
                        
                        if($this->anophtalmosMicrophthalmos !== null){
                            $fpdf->Image( $this->anophtalmosMicrophthalmos , 34, 46, 78, 4.5);
                        }
                        
                        if($this->disorganisedGlobePhthisis !== null){
                            $fpdf->Image( $this->disorganisedGlobePhthisis , 34, 51, 78, 5);
                        }
                        
                        if($this->primaryCongenitalInfantileGlaucoma !== null){
                            $fpdf->Image( $this->primaryCongenitalInfantileGlaucoma , 34, 62.5, 78, 4.5);
                        } 
                    }     
                }
                
                if($page == 5){
                    
                    if(!$cviElements['Consent_to_GP']){
                        $fpdf->Image( $this->gpConsentImage , 6, 21, 190, 7);
                    }
                    
                    if(!$cviElements['Consent_to_Local_Council']){
                        $fpdf->Image( $this->laConsentImage , 6, 77, 198, 21);
                    }
                    
                    if(!$cviElements['Consent_to_RCO']){
                        $fpdf->Image( $this->rcConsentImage , 6, 146, 198, 18);
                    }
                     
                    $patientGDimage = $this->generateGDFromSignature( $cviElements['PatientSignature'] );
                    $this->changePatientImageFromGDObject('patient_signature_'.$rand, $patientGDimage);
                    
                    $fpdf->Image( $this->patientSignatureImage , 90, 210, 100, 35);
                    
                    /**
                     * Set "signed by" image from text 
                     */
                    if( $this->createSignedByImage( $cviElements['signed_by'] )){
                        $fpdf->Image( $this->signedByImage , 9, 207, 65, 22);
                    }
                }
            }

            $fpdf->Output('F',$this->pdfOutput);
            
            $this->setDiagnosisPagesForPatient( $cviElements );
            
            return true;
        }
    }
    
    /**
     * Set diagnosis on diagnosis page when patient under the age of 18
     * @param type $cviElements
     */
    private function setDiagnosisUnder18( $cviElements )
    {

        switch($cviElements['SelectedVisualPathwayProblem']){
            case 44:
                $this->centralVisualPathwayPromblems = realpath(__DIR__ . '/..') . '/assets/img/acuity.png';
                break;
            case 45:
                $this->centralVisualPathwayPromblems = realpath(__DIR__ . '/..') . '/assets/img/fields.png';
                break;
            case 46:
                $this->centralVisualPathwayPromblems = realpath(__DIR__ . '/..') . '/assets/img/visual_perception.png';
                break;
            default: 
                $this->centralVisualPathwayPromblems = realpath(__DIR__ . '/..') . '/assets/img/central_visual_pathway_problems.png';
                break;
        }
        
        if( $cviElements['SelectedAnophthalmosMicrophthalmos'] > 0){
            switch($cviElements['SelectedAnophthalmosMicrophthalmos']){
                case 48:
                    $this->anophtalmosMicrophthalmos = realpath(__DIR__ . '/..') . '/assets/img/microphthalmos.png'; 
                    break;
                case 49:
                    $this->anophtalmosMicrophthalmos = realpath(__DIR__ . '/..') . '/assets/img/anophthalmos.png';
                    break;
            }
        }
        
        if( $cviElements['SelectedDisorganisedglobePhthisis'] > 0){
            switch($cviElements['SelectedDisorganisedglobePhthisis']){
                case 50:
                    $this->disorganisedGlobePhthisis = realpath(__DIR__ . '/..') . '/assets/img/disorganised_globe.png';
                    break;
                case 51:
                    $this->disorganisedGlobePhthisis = realpath(__DIR__ . '/..') . '/assets/img/phthisis.png';
                    break;
            }
        }
        
        if( $cviElements['SelectedPrimaryCongenitalInfantileGlaucoma'] > 0){
            switch($cviElements['SelectedPrimaryCongenitalInfantileGlaucoma']){
                case 53:
                    $this->primaryCongenitalInfantileGlaucoma = realpath(__DIR__ . '/..') . '/assets/img/primary_congenital.png';
                    break;
                case 54:
                    $this->primaryCongenitalInfantileGlaucoma = realpath(__DIR__ . '/..') . '/assets/img/infantile_glaucoma.png';
                    break;
            }
        }
        
        return true;
    }
    
    /**
     * Remove page 3 if patient under 18, hide part of page 2 if patient over 18
     * @param type $cviElements
     */
    private function setDiagnosisPagesForPatient( $cviElements )
    {

        $fpdf = new \FPDI();
        $pageCount = $fpdf->setSourceFile( $this->pdfOutput );
        
        $skipPages = [3];
       
        for( $page=1; $page<=$pageCount; $page++ )
        {
            //If patient over 18
            if($cviElements['patient_type'] == 0){
                //  Skip undesired pages
                if( in_array($page,$skipPages) )
                    continue;
            } else {
                //Actually this is page 2
                if($page == 3){
                    $fpdf->Image( $this->diagnosisImage , 0, 70, 210, 220);
                }       
            }

            $templateID = $fpdf->importPage($page);
            $fpdf->getTemplateSize($templateID);
            $fpdf->addPage();
            $fpdf->useTemplate($templateID);
        }

        $fpdf->Output('F',$this->pdfOutput);
    }
    
    /**
     * Get CVI print elements in array
     * @param type $event
     * @return array
     */
    public function generateCviElementsForPDF( $event )
    {
        $info = $this->getElementForEvent( $event , 'Element_OphCoCvi_EventInfo_V1' )->getElementsForCVIpdf();
        $demographics = $this->getElementForEvent( $event , 'Element_OphCoCvi_Demographics_V1')->getElementsForCVIpdf();
        $clinical = $this->getElementForEvent( $event , 'Element_OphCoCvi_ClinicalInfo_V1')->getElementsForCVIpdf();
        $clerical = $this->getElementForEvent( $event , 'Element_OphCoCvi_ClericalInfo_V1' )->getElementsForCVIpdf();
        $consentSignature = $this->getElementForEvent( $event , 'Element_OphCoCvi_PatientSignature')->getElementsForCVIpdf();
        $consultantSignature = $this->getElementForEvent( $event , 'Element_OphCoCvi_ConsultantSignature')->getElementsForCVIpdf();
        
        $cviElements = array_merge($info, $demographics, $clinical, $clerical, $consentSignature, $consultantSignature);
        
        return $cviElements;
    }
    
    /**
     * Create image from "Signed By" text
     * @param type $text
     * @return boolean
     */
    private function createSignedByImage( $text )
    {
        $image_width = 400;
     
        $this->signedByImage = $this->outDir.'signedBy_' . mt_rand() .'.png';
        
        $font = $this->yii->basePath.'/assets/fonts/Roboto/Roboto-Regular.ttf';
        $line_height = 20;
        $padding = 20;
        $font_size = 12;
       
        $lines = explode("\n", $text);
        $image = imagecreate($image_width,((count($lines) * $line_height)) + ($padding * 2));
        $background = imagecolorallocate($image, 255, 255, 255);
        $colour = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $background);
        $i = $padding;
        foreach($lines as $line){
            imagettftext($image, $font_size, 0, 10, $i, $colour, $font, trim($line));
            $i += $line_height;
        }

        imagepng($image, $this->signedByImage);
        imagedestroy($image);
        
        return true;
    }
    
    /**
     * Generate GD source from base64 decoded image
     * if source image is missing (e.g. patient signature, the image will be an 1x1 image with transparent bg )
     * @param type $sourceImage
     * @return type
     */
    public function generateGDFromSignature( $sourceImage )
    {
        if ($sourceImage) {
            $signature = imagecreatefromstring($sourceImage);
        } else {
            $signature = imagecreatetruecolor(1, 1);
            $color = imagecolorallocatealpha($signature, 0, 0, 0, 127); 
            imagefill($signature, 0, 0, $color);
            imagesavealpha($signature, true);
        }
        return $signature;
    }


    /**
     * Change an existing image in document by GD object
     * @param $imageName
     * @param $image
     */
    
    public function changePatientImageFromGDObject($imageName, $image)
    {
        if ($image !== false) {
            $this->patientSignatureImage = $this->outDir . $imageName.'.png';
            imagepng($image, $this->patientSignatureImage );
            imagedestroy($image);
        }
    }
    
    /**
     * Change an existing image in document by GD object
     * @param $imageName
     * @param $image
     */
    
    public function changeConsultantImageFromGDObject($imageName, $image)
    {
        if ($image !== false) {
            $this->consultantSignatureImage = $this->outDir . $imageName.'.png';
            imagepng($image, $this->consultantSignatureImage );
            imagedestroy($image);
        }
    }
    
    /**
     * Get generated v1 pdf
     */
    public function getConsentPDF()
    {
        $file = \ProtectedFile::createFromFile( $this->pdfOutput );
        $file->save();
        $this->clearImages();

        return $file;
    }
    
    /**
     * Delete copied signatures images after print
     */
    public function clearImages()
    {
        if($this->consultantSignatureImage){
            unlink( $this->consultantSignatureImage );
        }
        if($this->patientSignatureImage){
            unlink( $this->patientSignatureImage );
        }
        
        if($this->signedByImage){
            unlink($this->signedByImage);
        }
    }

    public function createConsentPdf(\Event $event)
    {
        if(!is_dir($this->outDir)){
            mkdir($this->outDir, 0777, true);
        }
        $pdf = new Pdf(\Yii::getPathOfAlias("application.modules.OphCoCvi.views.odtTemplate")."/cvi_consent.pdf");

        /** @var Element_OphCoCvi_Demographics_V1 $info */
       // $info = $this->getDemographicsElementForEvent($event);
        $rand = uniqid();
        $tmp_name = "/tmp/OphCoCvi_cvi_consent_".$rand.".pdf";
        
        $consentSignature = $this->getElementForEvent( $event , 'Element_OphCoCvi_PatientSignature')->getElementsForCVIpdf();
        
        $pdf->fillForm($consentSignature)
        ->needAppearances()
        ->flatten()
        ->saveAs($tmp_name);
        
        $fpdf = new \FPDI();

        $pagecount = $fpdf->setSourceFile( $tmp_name );

        for ($page = 1; $page <= $pagecount; $page++) {
            $fpdf->importPage($page);
            $fpdf->AddPage();
            $fpdf->useTemplate($page);

            if(!$consentSignature['Consent_to_GP']){
                $fpdf->Image( $this->gpConsentImage , 6, 21, 190, 7);
            }

            if(!$consentSignature['Consent_to_Local_Council']){
                $fpdf->Image( $this->laConsentImage , 6, 77, 198, 21);
            }

            if(!$consentSignature['Consent_to_RCO']){
                $fpdf->Image( $this->rcConsentImage , 6, 146, 198, 18);
            }
            
            $patientGDimage = $this->generateGDFromSignature( $consentSignature['PatientSignature'] );
            $this->changePatientImageFromGDObject('patient_signature_'.$rand, $patientGDimage);
                    
            $fpdf->Image( $this->patientSignatureImage , 80, 210, 110, 35);
            
            if( $consentSignature['signed_by'] !== "" ){
                $this->createSignedByImage( $consentSignature['signed_by']);
                $fpdf->Image( $this->signedByImage , 9, 207, 65, 22);
            }
        }

        $fpdf->Output('F',$tmp_name);

        return $tmp_name;
    }

    private function deliveryStatusText($status_code)
    {
        switch ($status_code) {
            case Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_PENDING:
                return "Pending";
                break;

            case Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_SENT:
                return "Sent";
                break;

            case Element_OphCoCvi_EventInfo_V1::DELIVERY_STATUS_ERROR:
                return "Error";
                break;

            default:
                return "Unknown";
                break;
        }
    }

    public function getGPDeliveryStatus(\Event $event)
    {
        if(!$info = $this->getEventInfoElementForEvent($event)) {
            return "Unknown";
        }

        if($info->gp_delivery == 0) {
            return "Not consented or disabled by configuration";
        }

        return $this->deliveryStatusText($info->gp_delivery_status);
    }

    public function getLADeliveryStatus(\Event $event)
    {
        if(!$info = $this->getEventInfoElementForEvent($event)) {
            return "Unknown";
        }

        if($info->la_delivery == 0) {
            return "Not consented or disabled by configuration";
        }

        return $this->deliveryStatusText($info->la_delivery_status);
    }

    public function getRCOPDeliveryStatus(\Event $event)
    {
        if(!$info = $this->getEventInfoElementForEvent($event)) {
            return "Unknown";
        }

        if($info->rco_delivery == 0) {
            return "Not consented or disabled by configuration";
        }

        return $this->deliveryStatusText($info->rco_delivery_status);
    }

    /**
     * @param \Event $event
     * @return bool
     */

    public function sendNotification(\Event $event)
    {
        $creator = $event->user;
        $roles = array_keys($creator->getRoles());

        if(in_array("Clinical CVI", $roles)) {
            return $this->sendNotificationToClericalOfficer($event);
        }
        else if(in_array("Clerical CVI", $roles)) {
            return $this->sendNotificationToClinician($event);
        }

        return false;
    }

    /**
     * @param \Event $event
     * @return bool
     */

    private function sendNotificationToClinician(\Event $event)
    {
        /** @var Element_OphCoCvi_EventInfo_V1 $info_element */
        $info_element = $this->getEventInfoElementForEvent($event);
        $firm = $info_element->consultantInChargeOfThisCvi;
        if(!is_null($firm) && $consultant = $firm->consultant) {
            $msg_type = $type = OphCoMessaging_Message_MessageType::model()->findByAttributes(array('name' => 'General'));
            $messenger = new MessageCreator($event->episode, $event->user, $consultant, $msg_type);
            $messenger->setMessageTemplate('application.modules.OphCoMessaging.views.templates.cvi');
            $messenger->setMessageData(array(
                'recipient' => $consultant,
                'patient' => $event->episode->patient,
            ));
            try {
                $messenger->save('', array('event' => $event->id));
                return true;
            }
            catch (\Exception $e) {
                \Yii::log($e->getMessage(), \CLogger::LEVEL_ERROR);
                return false;
            }
        }
        else {
            // There's no one to notify...
            return false;
        }
    }

    /**
     * @param \Event $event
     * @return bool
     */

    private function sendNotificationToClericalOfficer(\Event $event)
    {
        if(!isset(\Yii::app()->params['new_cvi_notification_email']) || !\Yii::app()->params['new_cvi_notification_email']) {
            return false;
        }

        $message = \Yii::app()->mailer->newMessage();
        $message->setFrom(isset(\Yii::app()->params['from_email']) ? \Yii::app()->params['from_email'] : "noreply@openeyes.org.uk");
        $message->setTo(\Yii::app()->params["eclo_email"]);
        $message->setSubject("New CVI");
        $message->setBody("Dear ECLO Team\nA new CVI has been started by {$event->user->getFullName()}\nPatient: {$event->episode->patient->getFullName()}\nHos num:{$event->episode->patient->hos_num}");

        return \Yii::app()->mailer->sendMessage($message);
    }
}
