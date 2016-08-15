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
    protected $yii;

    public function __construct(\CApplication $yii = null)
    {
        if (is_null($yii)) {
            $yii = \Yii::app();
        }

        $this->yii = $yii;
    }

    /**
     * Get all events regardless of episode.
     *
     * @param Patient $patient
     * @return \Event[]
     * @throws \Exception
     */
    public function getEvents(Patient $patient)
    {
        return $this->getManager()->getEventsForPatient($patient);
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
     * @var OphCoCvi_Manager
     */
    protected $cvi_manager;

    /**
     * @return OphCoCvi_Manager
     */
    public function getManager()
    {
        if (!isset($this->cvi_manager)) {
            $this->cvi_manager = new OphCoCvi_Manager($this->yii, $this->getEventType());
        }

        return $this->cvi_manager;
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
            $rows[] = array(
                'date' => $this->getManager()->getDisplayStatusDateForEvent($event),
                'status' => $this->getManager()->getDisplayStatusForEvent($event),
                'event_url' => $this->getManager()->getEventViewUri($event)
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
        uasort($rows, function ($a, $b) {
            return $a['date'] < $b['date'] ? -1 : 1;
        });

        $params = array(
            'rows' => $rows,
            'oph_info_editable' => $oph_info_editable,
            'oph_info' => $info,
            'new_event_uri' => $this->yii->createUrl($this->getEventType()->class_name . '/default/create') . '?patient_id=' . $patient->id

        );

        return $this->renderPartial('OphCoCvi.views.patient.cvi_status', $params);
    }

    /**
     * @param $event
     */
    public function getUniqueCodeForCviEvent($event){
        $eventUniqueCodeId = \UniqueCodeMapping::model()->findAllByAttributes(array('event_id' => $event->id));
        $eventUniqueCode = \UniqueCodes::model()->findByPk($eventUniqueCodeId[0]->unique_code_id);

        $salt = (isset(\Yii::app()->params['portal']['credentials']['client_id'])) ? \Yii::app()->params['portal']['credentials']['client_id'] : '';
        $check_digit1 = new \CheckDigitGenerator(\Yii::app()->params['institution_code'].$eventUniqueCode->code, $salt);
        $check_digit2 = new \CheckDigitGenerator($eventUniqueCode->code.$event->episode->patient->dob, $salt);
        $finalEventUniqueCode = \Yii::app()->params['institution_code'].$check_digit1->generateCheckDigit().'-'.$eventUniqueCode->code.'-'.$check_digit2->generateCheckDigit();

        return $finalEventUniqueCode;
    }
}