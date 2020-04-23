<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\OCT as OCTElement;
use OEModule\OphGeneric\models\Assessment as GenericAssessment;

class OCT extends \BaseEventElementWidget
{
    public static $moduleName = 'OphCiExamination';
    public $assessments = [];

    /**
     * @return FamilyHistoryElement
     */
    protected function getNewElement()
    {
        return new OCTElement();
    }

    public function init()
    {
        $this->assessments = $this->getAssessments();
        parent::init();
    }

    /**
     * @param AllergiesElement $element
     * @param $data
     * @throws \CException
     */
    protected function updateElementFromData($element, $data)
    {
    }

    public function getAssessments()
    {
        $criteria = new \CDbCriteria();
        $criteria->with = ['event.episode'];
        $criteria->together = true;
        $criteria->addCondition('patient_id = :patient_id');
        $criteria->params[':patient_id'] = $this->patient->id;
        $criteria->order = 'event.event_date ASC';

        return GenericAssessment::model()->findAll($criteria);
    }
}
