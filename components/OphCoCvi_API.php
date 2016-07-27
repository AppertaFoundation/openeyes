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

use \Patient;
use OEModule\OphCoCvi\models\Element_OphCoCvi_EventInfo;
use OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo;

class OphCoCvi_API extends \BaseAPI
{
	public function __construct(CApplication $yii = null)
	{
		if (is_null($yii)) {
			$yii = \Yii::app();
		}

		$this->yii = $yii;
	}

	/**
	 * Get all events regardless of episode.
	 *
	 * @TODO move to core?
	 * @param Patient $patient
	 * @return \Event[]
	 * @throws \Exception
	 */
	public function getEvents(Patient $patient)
	{
		$event_type = $this->getEventType();

		return \Event::model()->getEventsOfTypeForPatient($event_type, $patient);
	}

	/**
	 * Convenience wrapper to allow template rendering.
	 *
	 * @param $view
	 * @param array $parameters
	 * @return mixed
	 */
	protected function renderPartial($view, $parameters = array())
	{
		return $this->yii->controller->renderPartial($view, $parameters, true);
	}

	/**
	 * Render a patient summary widget to display CVI status based on the eCVI event and the core static model.
	 *
	 * @param Patient $patient
	 * @return string
	 */
	public function patientSummaryRender(Patient $patient)
	{
		$rows = array();
		$oph_info_editable = false;

		foreach ($this->getEvents($patient) as $event) {
			$info = Element_OphCoCvi_EventInfo::model()->findByAttributes(array('event_id' => $event->id));
			$clinical = Element_OphCoCvi_ClinicalInfo::model()->findByAttributes(array('event_id' => $event->id));

			$rows[] = array(
				'date' => $clinical->examination_date,
				'status' => $clinical->getStatus() . ' (' . ($info->is_draft ? 'Draft' : 'Issued') . ')',
				'event_url' => $this->yii->createUrl($event->eventType->class_name.'/default/view/'.$event->id)
			);
		}

		$info = $patient->getOphInfo();
		if (!count($rows) || !$info->isNewRecord) {
			$oph_info_editable = true;
			$rows[] = array(
				'date' => $info->cvi_status_date,
				'status' => $info->cvi_status->name
			);
		}

		// slot the info record into the right place
		uasort($rows, function ($a, $b) { return $a['date'] < $b['date'] ? -1 : 1; });

		$params = array(
			'rows' => $rows,
			'oph_info_editable' => $oph_info_editable,
			'oph_info' => $info,
			'new_event_uri' => $this->yii->createUrl($this->getEventType()->class_name.'/default/create').'?patient_id='.$patient->id

		);

		return $this->renderPartial('OphCoCvi.views.patient.cvi_status', $params);
	}
}