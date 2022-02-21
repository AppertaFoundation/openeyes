<?php
/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class BaseAPI
{
    /**
     * @var CApplication
     */
    protected $yii;

    /**
     * @var DataContext
     */
    protected $current_context;

    /**
     * BaseAPI constructor.
     * @param DataContext|null $context
     */
    public function __construct(CApplication $yii = null, DataContext $context = null)
    {
        if ($yii === null) {
            $yii = Yii::app();
        }
        $this->yii = $yii;
        $this->current_context = $context;
    }

    /**
     * @var EventType
     */
    private $event_type;

    /**
     * Returns the non-namespaced module class of the module API Instance.
     *
     * @return mixed
     */
    protected function getModuleClass()
    {
        return preg_replace('/^(.*\\\\)?(.*)_API$/', '$2', get_class($this));
    }

    /**
     * gets the event type for the api instance.
     * @return EventType $event_type
     * @throws Exception
     */
    public function getEventType()
    {
        if (!$this->event_type) {
            $module_class = $this->getModuleClass();
            if (!$this->event_type = EventType::model()->find('class_name=?', array($module_class))) {
                throw new Exception("Module is not migrated: $module_class");
            }
        }
        return $this->event_type;
    }

    /**
     * @param Patient $patient
     * @param null $before
     * @param bool $visible
     * @return CDbCriteria
     */
    protected function constructEventCriteria(\Patient $patient, $before = null, $visible = false)
    {
        $event_type = $this->getEventType();
        $criteria = new CDbCriteria();
        $criteria->compare('t.deleted', 0);
        $criteria->compare('episode.deleted', 0);
        $criteria->compare('event_type_id', $event_type->id);
        $criteria->compare('episode.patient_id', $patient->id);
        $criteria->order = 't.event_date desc, t.created_date desc';
        if ($before) {
            $criteria->compare('t.event_date', '<='.$before);
        }
        if ($visible) {
            $criteria->addCondition('episode.change_tracker IS NULL OR episode.change_tracker = false');
        }

        return $criteria;
    }

    /**
     * @param CDbCriteria $criteria
     * @return Event[]|null
     */
    protected function eventsWithCriteria(CDbCriteria $criteria)
    {
        return Event::model()->with(
            array('episode' =>
                array('with' =>
                    array(
                        'firm' => array(
                            'with' => 'serviceSubspecialtyAssignment'
                        ),
                        'patient'
                    )
                )
            ))->findAll($criteria);
    }

    /**
     * Get all the Events for this Patient, in order of event date, most recent first.
     * Defaults to returning all Events regardless of context.
     *
     * @param Patient $patient
     * @param bool $use_context
     * @param null $before
     * @param null $limit
     * @return Event[]|null
     */
    public function getEvents(\Patient $patient, $use_context = false, $before = null, $limit = null)
    {
        $criteria = $this->constructEventCriteria($patient, $before);

        if ($use_context) {
            $this->current_context->addEventConstraints($criteria);
        }

        if ($limit !== null) {
            $criteria->limit = $limit;
        }

        return $this->eventsWithCriteria($criteria);
    }

    /**
     * Returns events that are visible to the user only
     *
     * @param Patient $patient
     * @param bool $use_context
     * @param null $before
     * @param null $limit
     * @return Event[]|null
     */
    public function getVisibleEvents(\Patient $patient, $use_context = false, $before = null, $limit = null)
    {
        $criteria = $this->constructEventCriteria($patient, $before, true);

        if ($use_context) {
            $this->current_context->addEventConstraints($criteria);
        }

        if ($limit !== null) {
            $criteria->limit = $limit;
        }

        return $this->eventsWithCriteria($criteria);
    }

    /**
     * Returns the latest event for the event type of the API
     *
     * @param Patient $patient
     * @param bool $use_context
     * @param string $before - date formatted string
     * @return Event|null
     */
    public function getLatestEvent(Patient $patient, $use_context = false, $before = null)
    {
        $result = $this->getEvents($patient, $use_context, $before, 1);
        return count($result) ? $result[0] : null;
    }

    /**
     * Returns the latest visible event for the event type of the API
     *
     * @param Patient $patient
     * @param bool $use_context
     * @param null $before
     * @return null
     */
    public function getLatestVisibleEvent(Patient $patient, $use_context = false, $before = null)
    {
        $result = $this->getVisibleEvents($patient, $use_context, $before, 1);
        return count($result) ? $result[0] : null;
    }

    /**
     * Returns the given element type from the most recent Event for this module, if that element is present.
     * Otherwise will return null.
     *
     * @param $element
     * @param Patient $patient
     * @param boolean $use_context
     * @param string $before - date formatted string
     * @return BaseEventTypeElement|null
     */
    public function getElementFromLatestEvent($element, Patient $patient, $use_context = false, $before = null)
    {
        if ($event = $this->getLatestEvent($patient, $use_context, $before)) {
            $criteria = new CDbCriteria();
            $criteria->compare('event_id', $event->id);

            return $element::model()
                ->with('event')
                ->find($criteria);
        }
    }

    /**
     * Returns the given element type from the most recent Events that occurred on the same day for this module, if that element is present.
     * Otherwise will return null.
     *
     * @param $element
     * @param Patient $patient
     * @param boolean $use_context
     * @param string $before - date formatted string
     * @return BaseEventTypeElement|null
     */
    public function getElementFromLatestSameDayEvents($element, Patient $patient, $use_context = false, $before = null)
    {
        $events = $this->getEvents($patient, $use_context, $before);
        if ($events) {
            $latest_event = $events[0];
            $event_ids = [];

            ## Seeing as the events are in chronological order, same date events should be next to each other
            foreach($events AS $event){
                if ($event->event_date === $latest_event->event_date){
                    $event_ids[] = $event->id;
                } else {
                    ## therefore once the array index has moved passed relevant matches, all other events are irrelevant
                    break;
                }
            }
            $criteria = new CDbCriteria();
            $criteria->addInCondition('event_id', $event_ids);

            return $element::model()
                ->with('event')
                ->findAll($criteria);
        }
    }

    /**
     * @param $element
     * @param Patient $patient
     * @param bool $use_context
     * @param null $before
     * @return mixed
     */
    public function getElementFromLatestVisibleEvent($element, Patient $patient, $use_context = false, $before = null)
    {
        if ($event = $this->getLatestVisibleEvent($patient, $use_context, $before)) {
            $criteria = new CDbCriteria();
            $criteria->compare('event_id', $event->id);

            return $element::model()
                ->with('event')
                ->find($criteria);
        }
    }

    /**
     * Returns the most recent instances of the given element type for the Patient.
     *
     * @param $element
     * @param Patient $patient
     * @param bool $use_context
     * @param string $before - date formatted string
     * @return BaseEventTypeElement[]
     */
    public function getElements($element, Patient $patient, $use_context = false, $before = null, $criteria = null): array
    {
        if ($criteria === null) {
            $criteria = new CDbCriteria();
        }
        $criteria->compare('event.deleted', 0);
        $criteria->compare('episode.deleted', 0);
        $criteria->compare('episode.patient_id', $patient->id);
        $criteria->order = 'event.event_date desc, event.created_date desc';
        if ($before !== null) {
            $criteria->compare('event.event_date', '<='.$before);
        }
        if ($use_context) {
            $this->current_context->addEventConstraints($criteria);
        }

        return $element::model()
            ->with(array(
                'event' => array('with' => array(
                    'episode' =>
                        array('with' =>
                            array(
                                'firm' => array(
                                    'with' => array(
                                        'serviceSubspecialtyAssignment' => array(
                                            'with' => 'subspecialty'
                                        )
                                    )
                                ),
                                'patient'
                            )
                        )
                    )
                )
            ))
            ->findAll($criteria);
    }

    /**
     * Returns the most recent instance of the given element type if it exists.
     *
     * @param $element
     * @param Patient $patient
     * @param bool $use_context
     * @param string $before - date formatted string
     * @param string $after - date formatted string
     * @return BaseEventTypeElement|null
     */
    public function getLatestElement($element, Patient $patient, $use_context = false, $before = null, $after = null)
    {
        $criteria = new CDbCriteria();
        $criteria->limit = 1;
        if ($after !== null) {
            $criteria->compare('event.event_date', '>' . $after);
        }
        $result = $this->getElements($element, $patient, $use_context, $before, $criteria);
        return count($result) ? $result[0] : null;
    }

    /**
     * gets the element of type $element for the given patient in the given episode.
     *
     * @param Episode $episode - the episode
     * @param string  $element - the element class
     * @param string  $later_than - any English textual datetime description, this text will passed to strtotime, supported formats: http://php.net/manual/en/datetime.formats.php
     *
     * @return BaseEventTypeElement|null - Will actually be an instance of the class requested
     * @deprecated since 2.0
     */
    public function getElementForLatestEventInEpisode($episode, $element, $later_than = null)
    {
        trigger_error('getElementForLatestEventInEpisode is deprecated as of version 2.0, please use getElementFromLatestEvent instead', E_USER_NOTICE);
        $event_type = $this->getEventType();

        if ($event = $episode->getMostRecentEventByType($event_type->id)) {
            $criteria = new CDbCriteria();
            $criteria->compare('episode_id', $episode->id);
            $criteria->compare('event_id', $event->id);
            $criteria->order = 'event.created_date desc';

            if ($later_than && strtotime($later_than)){

                $later_than_date = date('Y-m-d H:i:s', strtotime("-3 weeks"));
                $criteria->addCondition('event.event_date >= :later_than');
                $criteria->params[':later_than'] = $later_than_date;
            }

            return $element::model()
                ->with('event')
                ->find($criteria);
        }
    }

    /**
     * gets all element of type $element for the given patient in the given episode.
     *
     * @param Episode $episode - the episode
     * @param string  $element - the element class
     *
     * @return BaseEventTypeElement|null - Will actually be an instance of the class requested
     * @deprecated - since 2.0
     */
    public function getElementForAllEventInEpisode($episode, $element)
    {
        $event_type = $this->getEventType();

        if ($events = $episode->getAllEventsByType($event_type->id))
        {
            foreach($events as $event)
            {
                $criteria = new CDbCriteria();
                $criteria->compare('episode_id', $episode->id);
                $criteria->compare('event_id', $event->id);
                $criteria->order = 'event.created_date desc';

                $result[] = $element::model()
                    ->with('event')
                    ->find($criteria);
            }
            return $result;
        }
    }

    /**
     * gets all the events in the episode for the event type this API is for, for the given patient, most recent first.
     *
     * @param Patient $patient - the patient
     * @param Episode $episode - the episode
     *
     * @return array - list of events of the type for this API instance
     * @deprecated - since 2.0
     */
    public function getEventsInEpisode($patient, $episode)
    {
        $event_type = $this->getEventType();

        if ($episode) {
            return $episode->getAllEventsByType($event_type->id);
        }

        return array();
    }

    /**
     * @param $episode_id
     * @param $event_type_id
     * @return Event
     * @deprecated since 2.0
     */
    public function getMostRecentEventInEpisode($episode_id, $event_type_id)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('event_type_id', $event_type_id);
        $criteria->compare('episode_id', $episode_id);
        $criteria->order = 'event_date desc, created_date desc';

        return Event::model()->find($criteria);
    }

    /**
     * gets the most recent instance of a specific element in the current episode
     * @param $episode_id
     * @param $event_type_id
     * @param $model
     * @param string $before_date
     * @return BaseEventTypeElement|false - Will actually be an instance of the class requested
     *
     * @deprecated - since 2.0
     */
    public function getMostRecentElementInEpisode($episode_id, $event_type_id, $model, $before_date = '')
    {
        $criteria = new CDbCriteria();
        $criteria->compare('event_type_id', $event_type_id);
        $criteria->compare('episode_id', $episode_id);
        if ($before_date) {
            $criteria->compare('event_date', '<='.$before_date);
        }
        $criteria->order = 'event_date desc, created_date desc';

        foreach (Event::model()->findAll($criteria) as $event) {
            if ($element = $model::model()->find('event_id=?', array($event->id))) {
                return $element;
            }
        }

        return false;
    }

    /**
     * Get the principal eye for the patient
     *
     * @param $patient
     * @param bool $use_context
     * @return Eye|null
     * @throws CException
     */
    public function getPrincipalEye($patient, $use_context=true)
    {
        if (!$use_context) {
            throw new CException('principal eye not supported for context-less requests');
        }
        return $this->current_context->getPrincipalEye($patient);
    }

    /**
     * @param string $prefix
     * @param Eye $eye
     * @return string
     */
    public function getEyeMethod($prefix, Eye $eye = null)
    {
        if ($eye && $postfix = Eye::methodPostFix($eye->id)) {
            return $prefix . $postfix;
        }
    }

    /**
     * Simple abstraction to support generic calls to functions based on the
     * principal eye from the current context (methods will be called with
     * the given $use_context value).
     *
     * @param $prefix
     * @param $patient
     * @param bool $use_context defaults to false
     * @return mixed
     * @throws \CException
     */
    protected function getMethodForPrincipalEye($prefix, $patient, $use_context = false)
    {
        $method = $this->getEyeMethod($prefix, $this->getPrincipalEye($patient, true));

        if ($method) {
            return $this->{$method}($patient, $use_context);
        }
    }

    /**
     * Gets the latest event by subspecialty's ref_spec code
     *
     * @param $patient
     * @param $subspecialty_ref_spec
     * @return Event|mixed|null
     */
    public function getLatestEventBySubspecialty($patient, $subspecialty_ref_spec):? \Event
    {
        $subspecialty_id = \Subspecialty::model()->findByAttributes(['ref_spec' => $subspecialty_ref_spec])->id ?? null;

        if (!$subspecialty_id) {
            return null;
        }

        $criteria = $this->constructEventCriteria($patient, null, true);
        $criteria->addCondition('subspecialty_id =:subspecialty_id');
        $criteria->params[':subspecialty_id'] = $subspecialty_id;

        $events = $this->eventsWithCriteria($criteria);
        return $events[0] ?? null;
    }

    /**
     * Gets the latest requested element in the requested subspecialty
     *
     * @param $patient
     * @param string $element
     * @param string $ref_spec
     * @return \CActiveRecord|null
     */
    public function getAllElementsBySubspecialty($patient, string $element, string $ref_spec): array
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('ref_spec =:ref_spec');
        $criteria->params[':ref_spec'] = $ref_spec;
        $criteria->limit = 1;

        return $this->getElements($element,
            $patient,
            false,
            null,
            $criteria
        );
    }

    /**
     * Gets the latest requested element in the requested subspecialty
     *
     * @param $patient
     * @param string $element
     * @param string $ref_spec
     * @return \CActiveRecord|null
     */
    public function getLatestElementBySubspecialty($patient, string $element, string $ref_spec):? \CActiveRecord
    {
        $elements = $this->getAllElementsBySubspecialty($patient, $element, $ref_spec);

        // [0] - because we applied limit=1,
        // the returned value is an array with one element on the 0 key,
        // or an empty array, but the ?? handles this
        return $elements[0] ?? null;
    }
}
