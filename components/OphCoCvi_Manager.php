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

class OphCoCvi_Manager extends \CComponent
{
    protected $yii;
    /**
     * @var \EventType
     */
    protected $event_type;

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
     * @param \Patient $patient
     * @return \Event[]
     */
    public function getEventsForPatient(\Patient $patient)
    {
        return \Event::model()->getEventsOfTypeForPatient($this->event_type, $patient);
    }

    protected $elements_for_events = array();

    /**
     * @param $event
     * @param $element_class
     * @return \CActiveRecord|null
     */
    protected function getElementForEvent($event, $element_class, $namespace = '\\OEModule\OphCoCvi\\models\\')
    {
        $cls = $namespace . $element_class;

        if (!isset($this->elements_for_events[$event->id])) {
            $elements_for_events[$event->id] = array();
        }

        if (!isset($this->elements_for_events[$event->id][$cls])) {
            $this->elements_for_events[$event->id][$cls] = $cls::model()->findByAttributes(array('event_id' => $event->id));
        }

        return $this->elements_for_events[$event->id][$cls];
    }

    /**
     * @param \Event $event
     * @return \CActiveRecord|null
     */
    public function getClinicalElementForEvent(\Event $event)
    {
        return $this->getElementForEvent($event, 'Element_OphCoCvi_ClinicalInfo');
    }

    /**
     * @param \Event $event
     * @return \CActiveRecord|null
     */
    public function getClericalElementForEvent(\Event $event)
    {
        return $this->getElementForEvent($event, 'Element_OphCoCvi_ClericalInfo');
    }


    /**
     * Generate the text display of the status of the CVI
     *
     * @param Element_OphCoCvi_ClinicalInfo $clinical
     * @param Element_OphCoCvi_ClericalInfo $clerical
     * @return string
     */
    protected function getDisplayStatus(Element_OphCoCvi_ClinicalInfo $clinical, Element_OphCoCvi_EventInfo $info)
    {
        return $clinical->getDisplayStatus() . ' (' . $info->getIssueStatusForDisplay() . ')';
    }

    /**
     * @param \Event $event
     * @return string
     */
    public function getDisplayStatusForEvent(\Event $event)
    {
        $clinical = $this->getElementForEvent($event, 'Element_OphCoCvi_ClinicalInfo');
        $info = $this->getElementForEvent($event, 'Element_OphCoCvi_EventInfo');

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
        $clinical = $this->getElementForEvent($event, 'Element_OphCoCvi_ClinicalInfo');
        return $clinical->examination_date;
    }

    /**
     * @param \Event $event
     * @return mixed|null
     */
    public function getDisplayIssueDateForEvent(\Event $event)
    {
        $info = $this->getElementForEvent($event, 'Element_OphCoCvi_EventInfo');
        return $info->getIssueDateForDisplay();
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
     * @param \CDbCriteria $criteria
     * @param array $filter
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
            $criteria->addCondition('event.event_date >= ?');
            $criteria->params[] = $from;
        } elseif ($to) {
            $criteria->addCondition('event.event_date <= ?');
            $criteria->params[] = $to;
        }
    }

    /**
     * @param \CDbCriteria $criteria
     * @param array $filter
     */
    private function handleConsultantListFilter(\CDbCriteria $criteria, $filter = array())
    {
        if (isset($filter['consultant_ids']) && strlen(trim($filter['consultant_ids']))) {
            $criteria->addInCondition('clinical_element.consultant_id', explode(",", $filter['consultant_ids']));
        }
    }

    /**
     * @param \CDbCriteria $criteria
     * @param array $filter
     */
    private function handleIssuedFilter(\CDbCriteria $criteria, $filter = array())
    {
        if (!isset($filter['show_issued']) || (isset($filter['show_issued']) && !(bool) $filter['show_issued'])) {
            $criteria->addCondition('t.is_draft = ?');
            $criteria->params[] = true;
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
            'clinical_element',
            'clinical_element.consultant',
            'clerical_element',
            'event.episode.patient.contact');

        $sort = new \CSort();

        $sort->attributes = array(
            'event_date' => array(
                'asc' => 'event.event_date asc, event.id asc',
                'desc' => 'event.event_date desc, event.id desc',
            ),
            'patient_name' => array(
                'asc' => 'lower(contact.last_name) asc, lower(contact.first_name) asc',
                'desc' => 'lower(contact.last_name) desc, lower(contact.first_name) desc',
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
                'desc' => 'is_draft asc, t.last_modified_date desc'
            ),
        );

        $criteria = $this->buildFilterCriteria($filter);

        return new \CActiveDataProvider($model, array(
            'sort' => $sort,
            'criteria' => $criteria
        ));
    }
}