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
class PatientPanelPopup extends BaseCWidget
{
    public $patient;

    protected function getWidgets() {
        $widgets = Yii::app()->params['patient_summary_id_widgets'];

        uasort($widgets, function($a, $b) {
            $orderA = isset($a['order']) ? $a['order'] : 0;
            $orderB = isset($b['order']) ? $b['order'] : 0;
            return $orderA - $orderB;
        });

        $rendered = [];

        foreach ($widgets as $w) {
            $output = $this->widget($w['class'], [
                'patient' => $this->patient,
            ], true);
            if ($output) {
                $rendered[] = $output;
            }
        }

        //Force forum to reload patient whenever the patient in OE changes
        if (Yii::app()->params['forum_force_refresh'] == 'on' && Yii::app()->params['enable_forum_integration'] == 'on') {
            // Check the patient number has changed since last load
            if ( !Yii::app()->user->hasState('last_patient') || (Yii::app()->user->hasState('last_patient') && Yii::app()->user->getState('last_patient') != $this->patient->hos_num )) {
                Yii::app()->clientScript->registerScript("forceforum", "oelauncher('forum');", CClientScript::POS_LOAD);
                // overwrite last patient id with current patient ID
                Yii::app()->user->setState('last_patient', $this->patient->hos_num);
            }
        }

        return $rendered;

    }

}
