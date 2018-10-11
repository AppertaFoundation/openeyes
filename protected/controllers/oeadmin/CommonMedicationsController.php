<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class CommonMedicationsController extends BaseAdminController
{

    public $group = 'Drugs';

    public function actionList()
    {
        $admin = new AdminListAutocomplete(CommonMedications::model(), $this);

        $admin->setModelDisplayName('Common Medications List');

        $admin->setListFields(array(
            'id',
            'medication_drug.name',
        ));

        $admin->setCustomDeleteURL('/oeadmin/CommonMedications/delete');
        $admin->setCustomSaveURL('/oeadmin/CommonMedications/add');

        $admin->setAutocompleteField(
            array(
                'fieldName' => 'medication_id',
                'jsonURL' => '/oeadmin/CommonMedications/search',
                'placeholder' => 'search for medication drug',
            )
        );
        $admin->div_wrapper_class = 'cols-5';
        $admin->listModel();
    }

    public function actionDelete($itemId)
    {
        /*
        * We make sure to not allow deleting directly with the URL, user must come from the commondrugs list page
        */
        if (!Yii::app()->request->isAjaxRequest) {
            $this->render('errorpage', array('errorMessage' => 'notajaxcall'));
        } else {
            if ($commonMedications = CommonMedications::model()->findByPk($itemId)) {
                $commonMedications->delete();
                echo 'success';
            } else {
                $this->render('errorpage', array('errormessage' => 'recordmissing'));
            }
        }
    }

    /**
     * @throws Exception
     */
    public function actionAdd()
    {
        $medicationId = $this->request->getParam('medication_id');
        if (!Yii::app()->request->isAjaxRequest) {
            $this->render('errorpage', array('errormessage' => 'notajaxcall'));
        } else {
            if (!is_numeric($medicationId)) {
                echo 'error';
            } else {
                $newCSD = new CommonMedications();
                $newCSD->medication_id = $medicationId;
                if ($newCSD->save()) {
                    echo 'success';
                } else {
                    echo 'error';
                }
            }
        }
    }

    public function actionSearch()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $criteria = new CDbCriteria();

            if (isset($_GET['term']) && strlen($term = $_GET['term']) > 0) {
                $criteria->addCondition(array('LOWER(name) LIKE :term'),
                    'OR');
                $params[':term'] = '%'.strtolower(strtr($term, array('%' => '\%'))).'%';
            }

            $criteria->order = 'name';
            $criteria->select = 'id, name';
            $criteria->params = $params;

            $drugs = MedicationDrug::model()->findAll($criteria);

            $return = array();
            foreach ($drugs as $drug) {
                $return[] = array(
                    'label' => $drug->name,
                    'value' => $drug->name,
                    'id' => $drug->id,
                );
            }
            echo CJSON::encode($return);
        }
    }
}
