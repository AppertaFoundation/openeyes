<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class AdditionalRisksController extends BaseAdminController
{
    public $group = 'Consent';

    /**
     * @throws Exception
     */
    public function actionList()
    {
        $additional_risk_model = OphTrConsent_AdditionalRisk::model();
        $institution_id = \Yii::app()->request->getParam('institution_id');
        if( $institution_id === null ){
            $institution_id = \Institution::model()->getCurrent()->id;
        }
        if(!$institution_id){
            throw new Exception('Missing institution ID');
        }
        if (Yii::app()->request->isPostRequest) {
            $subspecialty_ids = \Yii::app()->request->getPost('subspecialty-ids');
            $additional_risks = Yii::app()->request->getParam('OphTrConsent_AdditionalRisk');

            foreach ($additional_risks as $key => $additional_risk) {
                if (strlen($additional_risk['name'])===0) {
                    continue;
                }

                $additional_risk_object = $additional_risk_model->findByPk($additional_risk['id']);
                if (!$additional_risk_object) {
                    $additional_risk_object = new OphTrConsent_AdditionalRisk();
                }

                $additional_risk_object->name = $additional_risk['name'];
                $additional_risk_object->display_order = $additional_risk['display_order'];
                $additional_risk_object->institution_id = $institution_id;
                $additional_risk_object->active = $additional_risk['active'];

                if (!$additional_risk_object->save()) {
                    throw new Exception(
                        'Unable to save additional risks: ' .
                        print_r($additional_risk_object->getErrors(), true)
                    );
                } else {
                    $act_subspecialty_ids = isset($subspecialty_ids[$key]) ? $subspecialty_ids[$key] : [];
                    $additional_risk_object->saveAdditionalRiskSubspecialtyAssignments($act_subspecialty_ids);
                }
            }
            Yii::app()->user->setFlash('success', 'Common Systemic Disorder Group created');
        }

        $this->render('/oeadmin/additional_risks/index', [
            'additional_risks' => $additional_risk_model->findAll(
                'institution_id=:institution_id',
                [':institution_id'=>$institution_id]
            ),
            'subspecialty' => Subspecialty::model()->findAll(),
            'institution' => Institution::model()->findAll(),
            'institution_id' => $institution_id,
        ]);
    }
}
