<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class PatientPanel extends BaseCWidget
{

    public $patient;
    public $age;
    public $allow_clinical;

    public function init()
    {
        $this->allow_clinical = Yii::app()->user->checkAccess('OprnViewClinical');
        parent::init();
    }

    protected function getWidgets() {

        $widgets = Yii::app()->params['patient_summary_id_widgets'];

        uasort($widgets, function($a, $b) {
            $orderA = @$a['order'] ?: 0;
            $orderB = @$b['order'] ?: 0;
            return $orderA - $orderB;
        });

        $rendered = array();

        foreach ($widgets as $w) {

            $output = $this->widget($w['class'], array(
                'patient' => $this->patient,
            ), true);

            if ($output) {
                $rendered[] = $output;
            }
        }

        return $rendered;
    }
}