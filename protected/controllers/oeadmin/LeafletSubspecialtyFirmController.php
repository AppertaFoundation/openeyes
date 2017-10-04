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
class LeafletSubspecialtyFirmController extends BaseAdminController
{
    public function actionList()
    {
        $search = $this->request->getParam('search');
        $session = new CHttpSession();
        $session->open();
        $firmId = $this->request->getParam('firm_id');
        $subspecialtyId = $this->request->getParam('subspecialty_id');
        if ($firmId > 0 &&
            (isset($search['filterid']['subspecialty_id']['value']) && $search['filterid']['subspecialty_id']['value'] > 0)
        ) {
            $session['lastSubspecialtyId'] = $search['filterid']['subspecialty_id']['value'];
            $this->redirect('/oeadmin/LeafletSubspecialtyFirm/list?search[filterid][firm_id][value]='.$firmId.'&subspecialty_id='.$search['filterid']['subspecialty_id']['value']);
        }

        $lastSubspecialtyId = $session['lastSubspecialtyId'];
        // check if it's been already set
        if (!($lastSubspecialtyId > 0)) {
            if (isset($search['filterid']['subspecialty_id']['value'])) {
                $session['lastSubspecialtyId'] = $search['filterid']['subspecialty_id']['value'];
            } else {
                $session['lastSubspecialtyId'] = Firm::model()->findByPk(Yii::app()->session['selected_firm_id'])->serviceSubspecialtyAssignment->subspecialty_id;
            }
        }
        // || ($this->request->getParam("subspecialty_id")!=$session['lastSubspecialtyId'] && $search['filterid']['firm_id']['value']=="")
        if (($subspecialtyId > 0
                && $subspecialtyId != $session['lastSubspecialtyId']
                && isset($search['filterid']['firm_id']['value']))
            || ($subspecialtyId == $session['lastSubspecialtyId'] && $search['filterid']['firm_id']['value'] == '')
        ) {
            $session['lastSubspecialtyId'] = '';
            $this->redirect('/oeadmin/LeafletSubspecialtyFirm/list?search[filterid][subspecialty_id][value]='.$subspecialtyId);
        }

        if (isset($search['filterid']['firm_id']['value']) && $search['filterid']['firm_id']['value'] > 0) {
            $excludeSubspecialty = true;
            $excludeFirm = false;
            $admin = new AdminListAutocomplete(OphTrConsent_Leaflet_Firm::model(), $this);
            $admin->setCustomDeleteURL('/oeadmin/LeafletSubspecialtyFirm/deleteFirm');
        } else {
            $excludeSubspecialty = false;
            $excludeFirm = true;
            $admin = new AdminListAutocomplete(OphTrConsent_Leaflet_Subspecialty::model(), $this);
            $admin->setCustomDeleteURL('/oeadmin/LeafletSubspecialtyFirm/deleteSubspecialty');
        }

        $admin->setListFields(array(
            'id',
            'leaflet.name',
        ));
        $admin->setCustomSaveURL('/oeadmin/LeafletSubspecialtyFirm/add');
        $admin->setModelDisplayName('Leaflet-Subspecialty-Firm Assignment');
        if ($subspecialtyId > 0) {
            $defaultSubspecialty = $subspecialtyId;
        } else {
            $defaultSubspecialty = Firm::model()->findByPk(Yii::app()->session['selected_firm_id'])->serviceSubspecialtyAssignment->subspecialty_id;
        }

        $admin->setFilterFields(
            array(
                array(
                    'label' => 'Subspecialty',
                    'dropDownName' => 'subspecialty_id',
                    'defaultValue' => $defaultSubspecialty,
                    'listModel' => Subspecialty::model(),
                    'listIdField' => 'id',
                    'listDisplayField' => 'name',
                    'excludeSearch' => $excludeSubspecialty,
                ),
                array(
                    'label' => 'Firm',
                    'dropDownName' => 'firm_id',
                    'defaultValue' => null,
                    'listModel' => Firm::model(),
                    'listIdField' => 'id',
                    'listDisplayField' => 'name',
                    'emptyLabel' => '-- All --',
                    'dependsOnFilterName' => 'subspecialty_id',
                    'dependsOnDbFieldName' => 'subspecialty_id',
                    'dependsOnJoinedTable' => 'serviceSubspecialtyAssignment',
                    'excludeSearch' => $excludeFirm,
                ),
            )
        );
        // we set default search options
        if ($this->request->getParam('search') == '') {
            $admin->getSearch()->initSearch(array(
                    'filterid' => array(
                            'subspecialty_id' => Firm::model()->findByPk(Yii::app()->session['selected_firm_id'])->serviceSubspecialtyAssignment->subspecialty_id,
                        ),
                )
            );
        }
        $admin->setAutocompleteField(
            array(
                'fieldName' => 'leaflet_id',
                'allowBlankSearch' => 1,
                'jsonURL' => '/oeadmin/LeafletSubspecialtyFirm/search',
                'placeholder' => 'search for leaflets',
            )
        );
        //$admin->searchAll();
        $admin->listModel();
    }

    /**
     * @param $itemId
     * @param $model
     */
    protected function deleteItem($itemId, $model)
    {
        if ($leafletItem = $model->findByPk($itemId)) {
            $leafletItem->delete();
            echo 'success';
        } else {
            $this->render('errorpage', array('errormessage' => 'recordmissing'));
        }
    }

    /**
     * @param $itemId
     */
    public function actionDeleteFirm($itemId)
    {
        /*
        * We make sure to not allow deleting directly with the URL, user must come from the commondrugs list page
        */
        if (!Yii::app()->request->isAjaxRequest) {
            $this->render('errorpage', array('errorMessage' => 'notajaxcall'));
        } else {
            $this->deleteItem($itemId, OphTrConsent_Leaflet_Firm::model());
        }
    }

    /**
     * @param $itemId
     */
    public function actionDeleteSubspecialty($itemId)
    {
        /*
        * We make sure to not allow deleting directly with the URL, user must come from the commondrugs list page
        */
        if (!Yii::app()->request->isAjaxRequest) {
            $this->render('errorpage', array('errorMessage' => 'notajaxcall'));
        } else {
            $this->deleteItem($itemId, OphTrConsent_Leaflet_Subspecialty::model());
        }
    }

    public function actionAdd()
    {
        $firmId = $this->request->getParam('firm_id');
        $subspecialtyId = $this->request->getParam('subspecialty_id');
        $leafletId = $this->request->getParam('leaflet_id');
        if (!Yii::app()->request->isAjaxRequest) {
            $this->render('errorpage', array('errormessage' => 'notajaxcall'));
        } else {
            if (!is_numeric($leafletId)) {
                echo 'error 1';
            } elseif ($firmId > 0) {
                $newLFF = new OphTrConsent_Leaflet_Firm();
                $newLFF->firm_id = $firmId;
                $newLFF->leaflet_id = $leafletId;
                if ($newLFF->save()) {
                    echo 'success';
                } else {
                    echo 'error 2';
                }
            } elseif ($subspecialtyId > 0) {
                $newLFS = new OphTrConsent_Leaflet_Subspecialty();
                $newLFS->subspecialty_id = $subspecialtyId;
                $newLFS->leaflet_id = $leafletId;
                if ($newLFS->save()) {
                    echo 'success';
                } else {
                    echo 'error 3';
                }
            }
        }
    }

    public function actionSearch()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $criteria = new CDbCriteria();
            if (isset($_GET['term'])) {
                $term = $_GET['term'];
                $criteria->addCondition(array('LOWER(name) LIKE :term'),
                    'OR');
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
            echo CJSON::encode($return);
        }
    }
}
