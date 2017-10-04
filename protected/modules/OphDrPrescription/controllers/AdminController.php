<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2015
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2015, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class AdminController extends BaseAdminController
{
    /**
     * @description Common drugs administration page - it lists the common drugs based on site and subspecialty
     *
     * @return html (rendered page)
     */
    public function actionCommonDrugs()
    {
        /*
         * We try to set default values for the selects
         */
        if (isset($_GET['site_id'])) {
            $activeSite = $_GET['site_id'];
        } else {
            $activeSite = Yii::app()->session['selected_site_id'];
        }

        if (isset($_GET['subspecialty_id'])) {
            $activeSubspecialty = $_GET['subspecialty_id'];
        } else {
            $firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
            if (isset($firm->serviceSubspecialtyAssignment->subspecialty_id)) {
                $activeSubspecialty = $firm->serviceSubspecialtyAssignment->subspecialty_id;
            } else {
                $activeSubspecialty = null;
            }
        }

        $this->render('druglist', array(
            'selectedsite' => $activeSite,
            'selectedsubspecialty' => $activeSubspecialty,
            'site_subspecialty_drugs' => Element_OphDrPrescription_Details::model()->commonDrugsBySiteAndSpec($activeSite,
                $activeSubspecialty),
        ));
    }

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
            $this->render('errorpage', array('errorMessage' => 'notajaxcall'));
        } else {
            if ($site_subspec_drug = SiteSubspecialtyDrug::model()->findByPk($itemId)) {
                $site_subspec_drug->delete();
                echo 'success';
            } else {
                $this->render('errorpage', array('errormessage' => 'recordmissing'));
            }
        }
    }

    /**
     * @description Adds new drug into the site_subspecialty_drug table - AJAX call only
     *
     * @return string
     */
    public function actionCommonDrugsAdd()
    {
        $drugId = $this->request->getParam('drug_id');
        $siteId = $this->request->getParam('site_id');
        $subspecialtyId = $this->request->getParam('subspecialty_id');
        if (!Yii::app()->request->isAjaxRequest) {
            $this->render('errorpage', array('errormessage' => 'notajaxcall'));
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
        $this->genericAdmin('Edit prescription editing options', 'OphDrPrescriptionEditReasons',
            array(
               
            ));
    }

    public function actionTags()
    {
        $this->genericAdmin('Edit tags', 'Tag',
            array(
                'extra_fields'=>array(
                    array('field')
                )
            ));
    }

    public function actionDrugType()
    {
        $this->genericAdmin('Edit drug types', 'DrugType',
            array(
                'extra_fields' => array(
                    array('field' => 'tag_id',
                    'type' => 'lookup',
                    'model' => 'Tag')
                )
            ));
    }

}
