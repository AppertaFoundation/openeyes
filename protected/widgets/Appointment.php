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


class Appointment extends BaseCWidget
{

    public $patient;
    public $past_worklist_patients_count;
    public $worklist_patients;
    public $pro_theme = '';
    public $is_popup;

    public function init()
    {
        parent::init();

        // add OpenEyes.UI.RestrictedData js
        $assetManager = \Yii::app()->getAssetManager();
        $baseAssetsPath = \Yii::getPathOfAlias('application.assets.js');
        $assetManager->publish($baseAssetsPath, true);

        \Yii::app()->clientScript->registerScriptFile($assetManager->getPublishedUrl($baseAssetsPath, true).'/OpenEyes.UI.RestrictData.js', \CClientScript::POS_END);

        $criteria = new \CDbCriteria();
        $criteria->join = " JOIN worklist w ON w.id = t.worklist_id";

        $criteria_past = clone $criteria;
        $start_of_today = date("Y-m-d");

        $criteria->addCondition('t.when >= "' . $start_of_today . '"');
        $criteria->order = 't.when asc';

        $criteria_past->addCondition('t.when < "' . $start_of_today . '"');
        $criteria_past->order = 't.when desc';

        $this->worklist_patients = WorklistPatient::model()->findAllByAttributes(
            ['patient_id' => $this->patient->id],
            $criteria
        );
        $this->past_worklist_patients_count = WorklistPatient::model()->countByAttributes(
            ['patient_id' => $this->patient->id],
            $criteria_past
        );
    }

    public function render($view, $data = null, $return = false)
    {
        if (is_array($data)) {
            $data = array_merge($data, get_object_vars($this));
        } else {
            $data = get_object_vars($this);
        }

        parent::render($view, $data, $return);
    }

    public function run()
    {
        $this->render(get_class($this));
    }
}
