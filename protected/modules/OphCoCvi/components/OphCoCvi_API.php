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

use OEModule\OphCoCvi\models\Element_OphCoCvi_EventInfo_V1;
use \Patient;
use OEModule\OphCoCvi\models\Element_OphCoCvi_EventInfo;
use OEModule\OphCoCvi\models\Element_OphCoCvi_ClinicalInfo;
use OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity;

/**
 * Class OphCoCvi_API
 *
 * @package OEModule\OphCoCvi\components
 */
class OphCoCvi_API extends \BaseAPI
{
    protected $yii;

    /**
     * OphCoCvi_API constructor.
     *
     * @param \CApplication|null $yii
     */
    public function __construct(\CApplication $yii = null)
    {
        if (is_null($yii)) {
            $yii = \Yii::app();
        }

        $this->yii = $yii;
    }

    public $createOprn = 'OprnCreateCvi';
    public $createOprnArgs = array('user_id', 'firm', 'episode');

    /**
     * Convenience wrapper to allow template rendering.
     *
     * @param string $view
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
                'event_url' => $this->getManager()->getEventViewUri($event),
            );
        }

        $info = $patient->getOphInfo();

        if (!count($rows) || !$info->isNewRecord) {
            $oph_info_editable = true;
            $rows[] = array(
                'date' => $info->cvi_status_date,
                'status' => $info->cvi_status->name,
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
            'new_event_uri' => $this->yii->createUrl($this->getEventType()->class_name . '/default/create') . '?patient_id=' . $patient->id,

        );

        return $this->renderPartial('OphCoCvi.views.patient.cvi_status', $params);
    }

    /**
     * @param $event_id
     * @return bool
     */
    public function canUpdate($event_id)
    {
        if ($event = \Event::model()->findByPk($event_id)) {
            return $this->getManager()->canEditEvent($event);
        }

        return false;
    }

    /**
     * @param $event
     */
    public function getUniqueCodeForCviEvent($event)
    {
        $eventUniqueCodeId = \UniqueCodeMapping::model()->findAllByAttributes(array('event_id' => $event->id));
        $eventUniqueCode = \UniqueCodes::model()->findByPk($eventUniqueCodeId[0]->unique_code_id);

        $salt = (isset(\Yii::app()->params['portal']['credentials']['client_id'])) ? \Yii::app()->params['portal']['credentials']['client_id'] : '';
        $check_digit1 = new \CheckDigitGenerator(\Yii::app()->params['institution_code'] . $eventUniqueCode->code, $salt);
        $check_digit2 = new \CheckDigitGenerator($eventUniqueCode->code . $event->episode->patient->dob, $salt);
        $finalEventUniqueCode = \Yii::app()->params['institution_code'] . $check_digit1->generateCheckDigit() . '-'
            . $eventUniqueCode->code . '-' . $check_digit2->generateCheckDigit();

        return $finalEventUniqueCode;
    }

    /**
     * Checking if the patient has CVI
     *
     * @param Patient $patient
     * @return boolean
     */
    public function hasCVI(\Patient $patient)
    {
        if (count($this->getEvents($patient))) {
            return true;
        }
        $oph_info = $patient->getOphInfo();

        return !$oph_info->isNewRecord;
    }

    /**
     * Checks whether the VA value(s) is below the threshold
     *
     * @param int|array $va_base_value
     * @return bool true if value below the threshold
     */
    public function isVaBelowThreshold($va_base_value)
    {
        $threshold = $this->yii->params['thresholds']['visualAcuity']['alert_base_value'];

        if (is_array($va_base_value)) {
            $result = NULL;

            foreach ($va_base_value as $value) {
                if (is_numeric($value)) {
                    if ($value < $threshold) {
                        $result = is_null($result) ? true : $result;
                    } else {
                        $result = false;
                    }
                }
            }

            return is_null($result) ? false : $result;
        } else {
            return is_numeric($va_base_value) && ($va_base_value < $threshold);
        }
    }

    /**
     * @param Patient $patient
     * @param         $element
     * @param         $show_create - flag to indicate whether the create button should be shown
     * @return mixed
     */
    public function renderAlertForVA(Patient $patient, $element, $show_create = false)
    {
        $show_alert = false;
        $base_values = array();
        if ($element) {
            $show_alert = !$element->cvi_alert_dismissed && !$this->hasCVI($patient);
            foreach (array_merge($element->right_readings, $element->left_readings) as $reading) {
                $base_values[] = $reading->value;
            }
        }
        return $this->renderPartial('OphCoCvi.views.patient._va_alert', array(
            'element' => $element,
            'threshold' => $this->yii->params['thresholds']['visualAcuity']['alert_base_value'],
            'visible' => $show_alert && $this->isVaBelowThreshold($base_values),
            'has_cvi' => $this->hasCVI($patient),
            'show_create' => $show_create,
        ));
    }

    /**
     * @return \Event[]
     */

    public function getPendingDeliveryEvents()
    {
        $crit = new \CDbCriteria();
        $crit->join = "LEFT JOIN et_ophcocvi_eventinfo AS einfo ON einfo.event_id = t.id";
        $crit->addCondition("einfo.gp_delivery = 1 AND einfo.gp_delivery_status = 'PENDING'", "OR");
        $crit->addCondition("einfo.la_delivery = 1 AND einfo.la_delivery_status = 'PENDING'", "OR");
        $crit->addCondition("einfo.rco_delivery = 1 AND einfo.rco_delivery_status = 'PENDING'", "OR");
        $crit->addCondition("t.deleted = 0");

        return \Event::model()->findAll($crit);
    }
}
