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
class CommonSystemicDisorderController extends BaseAdminController
{
    public $group = 'Disorders';

    public function actionList()
    {
        $admin = new AdminListAutocomplete(CommonSystemicDisorder::model(), $this);

        $admin->setListFields(array(
            'id',
            'disorder.fully_specified_name',
        ));

        $admin->setCustomDeleteURL('/oeadmin/CommonSystemicDisorder/delete');
        $admin->setCustomSaveURL('/oeadmin/CommonSystemicDisorder/add');

        $admin->setModelDisplayName('Common Systemic Disorders');

        $admin->setAutocompleteField(
            array(
                'fieldName' => 'disorder_id',
                'jsonURL' => '/oeadmin/CommonSystemicDisorder/search',
                'placeholder' => 'search for systemic disorders',
            )
        );
        //$admin->searchAll();
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
            if ($commonSystemicDisorder = CommonSystemicDisorder::model()->findByPk($itemId)) {
                $commonSystemicDisorder->delete();
                echo 'success';
            } else {
                $this->render('errorpage', array('errormessage' => 'recordmissing'));
            }
        }
    }

    public function actionAdd()
    {
        $disorderId = $this->request->getParam('disorder_id');
        if (!Yii::app()->request->isAjaxRequest) {
            $this->render('errorpage', array('errormessage' => 'notajaxcall'));
        } else {
            if (!is_numeric($disorderId)) {
                echo 'error';
            } else {
                $newCSD = new CommonSystemicDisorder();
                $newCSD->disorder_id = $disorderId;
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
                $criteria->addCondition(array('LOWER(fully_specified_name) LIKE :term', 'LOWER(term) LIKE :term'),
                    'OR');
                $params[':term'] = '%'.strtolower(strtr($term, array('%' => '\%'))).'%';
            }

            $criteria->order = 'fully_specified_name';
            $criteria->select = 'id, fully_specified_name';
            $criteria->params = $params;

            $disorders = Disorder::model()->active()->findAll($criteria);

            $return = array();
            foreach ($disorders as $disorder) {
                $return[] = array(
                    'label' => $disorder->fully_specified_name,
                    'value' => $disorder->fully_specified_name,
                    'id' => $disorder->id,
                );
            }
            echo CJSON::encode($return);
        }
    }
}
