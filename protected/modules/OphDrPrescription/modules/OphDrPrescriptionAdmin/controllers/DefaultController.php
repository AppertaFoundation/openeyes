<?php

/**
 * (C) OpenEyes Foundation, 2018
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
class DefaultController extends BaseAdminController
{
    public $group = 'Drugs';

    /**
     * @description Deletes a drug from the site_subspecialty_drug table - AJAX call only
     *
     * @param $itemId
     *
     * @return html
     */
    public function actionCommonDrugsDelete($itemId)
    {
        /*
         * We make sure to not allow deleting directly with the URL, user must come from the commondrugs list page
         */
        if (!Yii::app()->request->isAjaxRequest) {
            $this->render('/default/errorpage', array('errorMessage' => 'notajaxcall'));
        } else {
            $site_subspec_drug = SiteSubspecialtyDrug::model()->findByPk($itemId);
            if ($site_subspec_drug) {
                $site_subspec_drug->delete();
                echo 'success';
            } else {
                $this->render('/default/errorpage', array('errormessage' => 'recordmissing'));
            }
        }
    }

    /**
     * @description Adds new drug into the site_subspecialty_drug table - AJAX call only
     * @throws Exception
     */
    public function actionCommonDrugsAdd()
    {
        $drugId = $this->request->getParam('drug_id');
        $siteId = $this->request->getParam('site_id');
        $subspecialtyId = $this->request->getParam('subspecialty_id');
        if (false && !Yii::app()->request->isAjaxRequest) {
            $this->render('/default/errorpage', ['errorMessage' => 'notajaxcall']);
        } else {
            if (!is_numeric($drugId) || !is_numeric($siteId) || !is_numeric($subspecialtyId)) {
                echo 'error';
            } else {
                $newSSD = new SiteSubspecialtyDrug();
                $newSSD->site_id = $siteId;
                $newSSD->subspecialty_id = $subspecialtyId;
                $newSSD->drug_id = $drugId;
                if ($newSSD->save()) {
                    echo 'success';
                } else {
                    echo 'error';
                }
            }
        }
    }

    public function actionPrescriptionEditOptions()
    {
        $this->genericAdmin(
            'Edit prescription editing options',
            'OphDrPrescriptionEditReasons',
            ['div_wrapper_class' => 'cols-5',
            'return_url'=>'../OphDrPrescription/admin/default/PrescriptionEditOptions'],
            null,
            true,
        );
    }

    public function actionTags()
    {
        $this->genericAdmin(
            'Edit tags',
            'Tag',
            array(
                'extra_fields'=>array(
                    array('field')
                )
            )
        );
    }

    public function actionDrugType()
    {
        $this->genericAdmin(
            'Edit drug types',
            'DrugType',
            array(
                'extra_fields' => array(
                    array('field' => 'tag_id',
                    'type' => 'lookup',
                    'model' => 'Tag')
                ),
                'div_wrapper_class' => 'cols-5',
            )
        );
    }

    public function actionDispenseCondition()
    {
        $this->render('/admin/dispense_condition/index');
    }

    public function actionDispenseLocation()
    {
        $this->render('/admin/dispense_location/index');
    }

}
