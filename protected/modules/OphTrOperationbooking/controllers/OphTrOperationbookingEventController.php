<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OphTrOperationbookingEventController extends BaseEventTypeController
{
    const ACTION_TYPE_SCHEDULE = 'Schedule';

    /**
     * Return the open referral choices for the patient.
     *
     * @return Referral[]
     */
    public function getReferralChoices($element = null)
    {
        $criteria = new CdbCriteria();
        $criteria->addCondition('patient_id = :pid');
        $criteria->addCondition('closed_date is null');
        $criteria->params = array('pid' => $this->patient->id);

        // if the referral has been closed but is the selected referral for the event, needs to be part of the list
        if ($element && $element->referral_id) {
            $criteria->addCondition('id = :crid', 'OR');
            $criteria->params[':crid'] = $element->referral_id;
        }

        $criteria->order = 'received_date DESC';

        return Referral::model()->findAll($criteria);
    }

    protected function beforeAction($action)
    {
        if (!$this->isPrintAction($action->id)) {
            Yii::app()->clientScript->registerScriptFile($this->assetPath . '/js/module.js');
        }

        return parent::beforeAction($action);
    }

    public function checkScheduleAccess($priority = false)
    {
        if ($this->event && !$this->event->isNewRecord && !$this->checkEditAccess()) {
            return false;
        }

        if (!$priority) {
            if ($eo = $this->getOpenElementByClassName('Element_OphTrOperationbooking_Operation')) {
                $priority = $eo->priority;
            }
        }

        if ($priority && $priority->schedule_authitem) {
            return $this->checkAccess($priority->schedule_authitem);
        }

        if ($this->event && $this->event->id) {
            return $this->checkEditAccess();
        }

        return $this->checkAccess('Edit');
    }
}
