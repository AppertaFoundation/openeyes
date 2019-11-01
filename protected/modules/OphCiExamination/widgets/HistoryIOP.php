<?php
/**
 * OpenEyes.
 *
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

use OEModule\OphCiExamination\models\HistoryIOP as HistoryIopElement;

class HistoryIOP extends \BaseEventElementWidget
{
    public static $moduleName = 'OphCiExamination';
    protected $print_view = 'HistoryIOP_event_print';
    /**
     * @return HistoryIopElement
     */
    protected function getNewElement()
    {
        return new HistoryIopElement();
    }

    /**
     * Basic setting of element attributes from provided data. Should be overridden to
     * handle any complex attributes
     *
     * @param $element
     * @param $data
     */
    protected function updateElementFromData($element, $data)
    {
    }

    public function getPastIOPs() {

        $exam_api = \Yii::app()->moduleAPI->get('OphCiExamination');
        $iops = $exam_api->getElements(
            'models\Element_OphCiExamination_IntraocularPressure',
            $this->patient,
            false,
            null,
            null);

        return $iops;
    }
}