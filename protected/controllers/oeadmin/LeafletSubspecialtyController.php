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
class LeafletSubspecialtyController extends BaseAdminController
{
    public function actionList()
    {
        $admin = new AdminListAutocomplete(OphTrConsent_Leaflet_Subspecialty::model(), $this);

        $admin->setListFields(array(
            'id',
            'leaflet.name',
        ));

        $admin->setCustomDeleteURL('/oeadmin/LeafletSubspecialty/delete');
        $admin->setCustomSaveURL('/oeadmin/LeafletSubspecialty/add');
        $admin->setModelDisplayName('Leaflet-Subspecialty Assignment');
        $admin->setFilterFields(
            array(
                array(
                    'label' => 'Subspecialty',
                    'dropDownName' => 'subspecialty_id',
                    'defaultValue' => Firm::model()->findByPk(Yii::app()->session['selected_firm_id'])->serviceSubspecialtyAssignment->subspecialty_id,
                    'listModel' => Subspecialty::model(),
                    'listIdField' => 'id',
                    'listDisplayField' => 'name',
                ),
            )
        );

        // we set default search options
        if ($this->request->getParam('search') == '') {
            $admin->getSearch()->initSearch(array(
                    'filterid' => array(
                            'subspecialty_id' => Firm::model()->findByPk(Yii::app()->session['selected_firm_id'])->serviceSubspecialtyAssignment->subspecialty_id,
                        ),
                ));
        }

        $admin->setAutocompleteField(
            array(
                'fieldName' => 'leaflet_id',
                'jsonURL' => '/oeadmin/LeafletSubspecialty/search',
                'placeholder' => 'search for leaflets',
            )
        );
        //$admin->searchAll();
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
            if ($leafletSubspecialy = OphTrConsent_Leaflet_Subspecialty::model()->findByPk($itemId)) {
                $leafletSubspecialy->delete();
                echo 'success';
            } else {
                $this->render('errorpage', array('errormessage' => 'recordmissing'));
            }
        }
    }

    public function actionAdd()
    {
        $subspecialtyId = $this->request->getParam('subspecialty_id');
        $leafletId = $this->request->getParam('leaflet_id');
        if (!Yii::app()->request->isAjaxRequest) {
            $this->render('errorpage', array('errormessage' => 'notajaxcall'));
        } else {
            if (!is_numeric($subspecialtyId) || !is_numeric($leafletId)) {
                echo 'error';
            } else {
                $newLFS = new OphTrConsent_Leaflet_Subspecialty();
                $newLFS->subspecialty_id = $subspecialtyId;
                $newLFS->leaflet_id = $leafletId;
                if ($newLFS->save()) {
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
                $criteria->addCondition(
                    array('LOWER(name) LIKE :term'),
                    'OR'
                );
                $params[':term'] = '%'.strtolower(strtr($term, array('%' => '\%'))).'%';
            }

            $criteria->order = 'name';
            $criteria->select = 'id, name';
            $criteria->params = $params;

            $results = OphTrConsent_Leaflet::model()->active()->findAll($criteria);

            $return = array();
            foreach ($results as $resultRow) {
                $return[] = array(
                    'label' => $resultRow->name,
                    'value' => $resultRow->name,
                    'id' => $resultRow->id,
                );
            }
            $this->renderJSON($return);
        }
    }
}
