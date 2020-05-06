<?php
/**
 * (C) Copyright Apperta Foundation 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */


class PatientController extends BaseApiController
{
    public function accessRules()
    {
        return [
            [
                'allow',
                'actions' => ['search'],
                'users' => ['@'],
            ],
            [
                'deny',
                'users' => ['*'],
            ],
        ];
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'BasicAuthBehavior' => ['class' => 'application.modules.Api.behaviors.BasicAuthBehavior'],
        ]);
    }

    public function actionSearch()
    {
        $term = trim(\Yii::app()->request->getParam('term', ''));

        $result = [];
        $patientSearch = new PatientSearch();
        if ($patientSearch->isValidSearchTerm($term)) {
            $dataProvider = $patientSearch->search($term);
            foreach ($dataProvider->getData() as $patient) {
                $result[] = [
                    'id' => $patient->id,
                    'first_name' => $patient->first_name,
                    'last_name' => $patient->last_name,
                    'age' => ($patient->isDeceased() ? 'Deceased' : $patient->getAge()),
                    'genderletter' => $patient->gender,
                    'dob' => $patient->dob,
                    'hos_num' => $patient->hos_num,
                    'nhsnum' => $patient->nhsnum,
                    'is_deceased' => $patient->is_deceased,
                ];
            }
        }

        $this->renderJSON(200, $result);
        Yii::app()->end();
    }
}
