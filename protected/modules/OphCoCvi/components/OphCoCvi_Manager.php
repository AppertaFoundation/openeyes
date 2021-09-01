<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


namespace OEModule\OphCoCvi\components;

use OEModule\OphCoCvi\models\Element_OphCoCvi_ClericalInfo;
use OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo;
use OEModule\OphCoCvi\models\Element_OphCoCvi_EventInfo;
use OEModule\OphCoCvi\models\Element_OphCoCvi_ConsentSignature;
use OEModule\OphCoCvi\models\Element_OphCoCvi_Demographics;

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
    private $input_template_file = 'cviTemplate.odt';

    /**
     * @param $status
     * @return string
     */
    public function getStatusText($status)
    {

        if ($status & self::$ISSUED) {
            return 'Issued';
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
            return 'Incomplete';
        } elseif (count($result) === 0) {
            return 'Complete';
        } else {
            return 'Incomplete/Missing: ' . implode(', ', $result);
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
     * @todo reconcile this with the newly introduced API method that achieves the same thing
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
        $core_class = 'Element_OphCoCvi_EventInfo';
        $namespaced_class = '\\OEModule\OphCoCvi\\models\\' . $core_class;

        $cls_rel_map = array(
            'Element_OphCoCvi_ClinicalInfo' => 'clinical_element',
            'Element_OphCoCvi_ClericalInfo' => 'clerical_element',
            'Element_OphCoCvi_ConsentSignature' => 'consent_element',
            'Element_OphCoCvi_Demographics' => 'demographics_element',
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
        return $this->getElementForEvent($event, 'Element_OphCoCvi_EventInfo');
    }

    /**
     * @param \Event $event
     * @return null|Element_OphCoCvi_ClinicalInfo
     */
    public function getClinicalElementForEvent(\Event $event)
    {
        return $this->getElementForEvent($event, 'Element_OphCoCvi_ClinicalInfo');
    }

    /**
     * @param \Event $event
     * @return null|Element_OphCoCvi_ClericalInfo
     */
    public function getClericalElementForEvent(\Event $event)
    {
        return $this->getElementForEvent($event, 'Element_OphCoCvi_ClericalInfo');
    }

    /**
     * @param \Event $event
     * @return null|Element_OphCoCvi_ConsentSignature
     */
    public function getConsentSignatureElementForEvent(\Event $event)
    {
        return $this->getElementForEvent($event, 'Element_OphCoCvi_ConsentSignature');
    }

    /**
     * @param \Event $event
     * @return Element_OphCoCvi_Demographics|null
     */
    public function getDemographicsElementForEvent(\Event $event)
    {
        return $this->getElementForEvent($event, 'Element_OphCoCvi_Demographics');
    }

    /**
     * Generate the text display of the status of the CVI
     *
     * @param Element_OphCoCvi_ClinicalInfo $clinical
     * @param Element_OphCoCvi_EventInfo    $info
     * @return string
     */
    protected function getDisplayStatus(Element_OphCoCvi_ClinicalInfo $clinical = null, Element_OphCoCvi_EventInfo $info)
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

    /**
     * @param \Event $event
     * @return bool
     */
    public function canIssueCvi(\Event $event)
    {
        if ($info = $this->getEventInfoElementForEvent($event)) {
            if (!$info->is_draft) {
                return false;
            }
        } else {
            return false;
        }

        if ($clinical = $this->getClinicalElementForEvent($event)) {
            $clinical->setScenario('finalise');
            if (!$clinical->validate()) {
                return false;
            }
            if (!$clinical->consultant_signature_file_id) {
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
        $data['hospitalNumber'] = \PatientIdentifierHelper::getIdentifierValue(\PatientIdentifierHelper::getIdentifierForPatient(Yii::app()->params['display_primary_number_usage_code'], $event->episode->patient->id, \Institution::model()->getCurrent()->id, \Yii::app()->session['selected_site_id']));

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
        $document = $this->populateCviCertificate($event);

        return $document->storePDF();
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

            $event->info = $this->getStatusText(self::$ISSUED);
            $event->save();

            $event->audit('event', 'cvi-issued', null, 'CVI Issued', array('user_id' => $user_id));

            $transaction->commit();

            $event->unlock();

            return true;
        } catch (\Exception $e) {
            \OELog::log($e->getMessage());
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
            $criteria->addCondition('t.site_id = :site_id');
            $criteria->params[':site_id'] = $filter['site_id'];
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
            $criteria->addInCondition('clinical_element.consultant_id', explode(',', $filter['consultant_ids']));
        }
    }


    /**
     * @param \CDbCriteria $criteria
     * @param array        $filter
     */
    private function handleIssuedFilter(\CDbCriteria $criteria, $filter = array())
    {
        if ((!array_key_exists('issue_complete', $filter) || (isset($filter['issue_complete']) && (bool)$filter['issue_complete']))
            and (!array_key_exists('issue_incomplete', $filter) || (isset($filter['issue_incomplete']) && (bool)$filter['issue_incomplete']))
            and (isset($filter['show_issued']) && (bool)$filter['show_issued'])) {
                $criteria->addCondition('t.is_draft = false OR event.info LIKE "Complete%" OR event.info LIKE "Incomplete%"');
        } elseif ((!array_key_exists('issue_complete', $filter) || (isset($filter['issue_complete']) && (bool)$filter['issue_complete']))
        and (!array_key_exists('issue_incomplete', $filter) || (isset($filter['issue_incomplete']) && (bool)$filter['issue_incomplete']))) {
            $criteria->addCondition('event.info LIKE "Complete%" OR event.info LIKE "Incomplete%"');
        } elseif ((!array_key_exists('issue_complete', $filter) || (isset($filter['issue_complete']) && (bool)$filter['issue_complete']))
            and (isset($filter['show_issued']) && (bool)$filter['show_issued'])) {
               $criteria->addCondition('t.is_draft = false OR event.info LIKE "Complete%"');
        } elseif ((!array_key_exists('issue_incomplete', $filter) || (isset($filter['issue_incomplete']) && (bool)$filter['issue_incomplete']))
            and (isset($filter['show_issued']) && (bool)$filter['show_issued'])) {
            $criteria->addCondition('t.is_draft = false OR event.info LIKE "Incomplete%"');
        } elseif ((!array_key_exists('issue_complete', $filter) || (isset($filter['issue_complete']) && (bool)$filter['issue_complete']))) {
            $criteria->addCondition('event.info LIKE "Complete%"');
        } elseif ((!array_key_exists('issue_incomplete', $filter) || (isset($filter['issue_incomplete']) && (bool)$filter['issue_incomplete']))) {
            $criteria->addCondition('event.info LIKE "Incomplete%"');
        } elseif (isset($filter['show_issued']) && (bool)$filter['show_issued']) {
            $criteria->addCondition('t.is_draft = false');
        }
    }

    /**
     * @param array $filter
     * @return \CDbCriteria
     */
    protected function buildFilterCriteria($filter = array())
    {
        $criteria = new \CDbCriteria();

        $this->handleDateRangeFilter($criteria, $filter);
        $this->handleSubspecialtyListFilter($criteria, $filter);
        $this->handleSiteListFilter($criteria, $filter);
        $this->handleCreatedByListFilter($criteria, $filter);
        $this->handleConsultantListFilter($criteria, $filter);
        $this->handleIssuedFilter($criteria, $filter);

        return $criteria;
    }

    /**
     * Abstraction of the list provider
     *
     * @param array $filter
     * @return \CActiveDataProvider
     */
    public function getListDataProvider($filter = array())
    {
        $model = Element_OphCoCvi_EventInfo::model()->with(
            'site',
            'user',
            'clinical_element',
            'clinical_element.consultant',
            'clerical_element',
            'event.episode.patient.contact',
            'event.episode.firm.serviceSubspecialtyAssignment.subspecialty'
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
            'creator' => array(
                'asc' => 'lower(user.last_name) asc, lower(user.first_name) asc, event.id asc',
                'desc' => 'lower(user.last_name) desc, lower(user.first_name) desc, event.id desc',
            ),
            'consultant' => array(
                'asc' => 'lower(consultant.last_name) asc, lower(consultant.first_name) asc, event.id asc',
                'desc' => 'lower(consultant.last_name) desc, lower(consultant.first_name) desc, event.id desc',
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

        return new \CActiveDataProvider($model, array(
            'sort' => $sort,
            'criteria' => $criteria,
        ));
    }

    /**
     * @param $event
     */
    public function updateEventInfo($event)
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
}
