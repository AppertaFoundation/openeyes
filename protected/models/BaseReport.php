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
class BaseReport extends CModel
{
    public $user_institution_id;
    public $user_selected_site_id = null;
    public $view;
    public $institution_id;

    public function getView()
    {
        if ($this->view) {
            return $this->view;
        }

        $model = CHtml::modelName($this);

        if (strstr($model, '_')) {
            $segments = explode('_', $model);

            $explode = explode('_', $model);
            $model = array_pop($explode);
        }

        return '_' . strtolower(preg_replace('/^Report/', '', $model));
    }

    public function attributeNames()
    {
    }

    protected function array2Csv(array $data)
    {
        if (count($data) == 0) {
            return;
        }
        ob_start();
        $df = fopen('php://output', 'w');
        foreach ($data as $row) {
            fputcsv($df, $row);
        }
        fclose($df);

        return ob_get_clean();
    }

    protected function setInstitutionAndSite()
    {
        $this->user_institution_id = Institution::model()->getCurrent()->id;
        $this->user_selected_site_id = Yii::app()->session['selected_site_id'];
    }

    public function getPatientIdentifierPrompt()
    {
        return PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(SettingMetadata::model()->getSetting('display_primary_number_usage_code'), $this->user_institution_id, $this->user_selected_site_id);
    }
}
