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

		if (!$event_type = \EventType::model()->find('class_name=?',array($module_class))) {
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

		if (!isset($this->elements_for_events[$event->id]))
			$elements_for_events[$event->id] = array();

		if (!isset($this->elements_for_events[$event->id][$cls]))
			$this->elements_for_events[$event->id][$cls] = $cls::model()->findByAttributes(array('event_id' => $event->id));

		return $this->elements_for_events[$event->id][$cls];
	}

	/**
	 * @param \Event $event
	 * @return string
	 */
	public function getDisplayStatusForEvent(\Event $event)
	{
		$clinical = $this->getElementForEvent($event, 'Element_OphCoCvi_ClinicalInfo');
		$info = $this->getElementForEvent($event, 'Element_OphCoCvi_EventInfo');

		return $clinical->getDisplayStatus() . ' (' . ($info->is_draft ? 'Draft' : 'Issued') . ')';
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

	public function getDisplayIssueDateForEvent(\Event $event)
	{
		$info = $this->getElementForEvent($event, 'Element_OphCoCvi_EventInfo');
		if ($info->is_draft) {
			return null;
		}
		else {
			// TODO: we probably need to actually be storing an issue date when the CVI is completed.
			return $info->last_modified_date;
		}
	}
	/**
	 * @param \Event $event
	 * @return string
	 */
	public function getEventViewUri(\Event $event)
	{
		return $this->yii->createUrl($event->eventType->class_name.'/default/view/'.$event->id);
	}

	public function getEventConsultant(\Event $event)
	{
		/**
		 * @var Element_OphCoCvi_ClinicalInfo
		 */
		$clinical = $this->getElementForEvent($event, 'Element_OphCoCvi_ClinicalInfo');

		return $clinical->consultant;
	}
}