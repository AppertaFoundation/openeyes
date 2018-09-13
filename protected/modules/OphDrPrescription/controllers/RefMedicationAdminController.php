<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class RefMedicationAdminController extends BaseAdminController
{
    public function actionList()
    {
        $admin = new Admin(RefMedication::model(), $this);
        $admin->setListFields(array(
            'id',
            'source_type',
            'preferred_term',
            'alternativeTerms',
            'vtm_term',
            'vmp_term',
            'amp_term',
        ));

        $admin->getSearch()->addSearchItem('preferred_term');

        $admin->setModelDisplayName('All Medications');

        $admin->listModel();
    }

    public function actionEdit($id)
    {
        $this->_getEditAdmin($id)->editModel();
    }

    private function _getEditAdmin($id)
    {
        $admin = new Admin(RefMedication::model(), $this);

        if(!is_null($id)) {
            $search_indexes = RefMedication::model()->findByPk($id)->refMedicationsSearchIndexes;
        }
        else {
            $search_indexes = array();
        }

        $admin->setEditFields(array(
            'preferred_term'=>'Preferred term',
            'short_term'=>'Short term',
            'preferred_code'=>'Preferred code',
            'source_type'=>'Source type',
            'source_subtype'=>'Source subtype',
            'vtm_term' => 'VTM term',
            'vtm_code' => 'VTM code',
            'vmp_term' => 'VMP term',
            'vmp_code' => 'VMP code',
            'amp_term' => 'AMP term',
            'amp_code' => 'AMP code',
            'alternative_terms' =>  array(
                'widget' => 'GenericAdmin',
                'options' => array(
                    'model' => RefMedicationsSearchIndex::class,
                    'label_field' => 'alternative_term',
                    'label_field_type' => 'text',
                    'items' => $search_indexes,
                    'filters_ready' => true,
                    'cannot_save' => true,
                    'no_form' => true,
                ),
                'label' => 'Alternative terms'
            ),
        ));

        $admin->setModelDisplayName("Medication");
        if($id) {
            $admin->setModelId($id);
        }

        $admin->setCustomSaveURL('/OphDrPrescription/refMedicationAdmin/save/'.$id);

        return $admin;
    }

    public function actionSave($id)
    {
        $admin = $this->_getEditAdmin($id);

        if(is_null($id)) {
            $model = new RefMedication();
        }
        else {
            if(!$model = RefMedication::model()->findByPk($id)) {
                throw new CHttpException(404, 'Page not found');
            }
        }

        /** @var RefMedication $model */

        $data = Yii::app()->request->getPost('RefMedication');
        $model->setAttributes($data);

        if(!$model->validate()) {
            $errors = $model->getErrors();
            $this->render($admin->getEditTemplate(), array('admin' => $admin, 'errors' => $errors));
            exit;
        }

        $model->save();

        $existing_ids = array();
        $updated_ids = array();
        foreach ($model->refMedicationsSearchIndexes as $alt_term) {
            $existing_ids[] = $alt_term->id;
        }

        $ids = Yii::app()->request->getPost('id');
        if(is_array($ids)) {
            foreach ($ids as $key => $rid) {
                if($rid === '') {
                    $alt_term = new RefMedicationsSearchIndex();
                }
                else {
                    $alt_term = RefMedicationsSearchIndex::model()->findByPk($rid);
                    $updated_ids[] = $rid;
                }

                $alt_term->setAttributes(array(
                    'ref_medication_id' => $model->id,
                    'alternative_term' => Yii::app()->request->getPost('alternative_term')[$key],
                ));

                $alt_term->save();
            }
        }

        $deleted_ids = array_diff($existing_ids, $updated_ids);
        if(!empty($deleted_ids)) {
            RefMedicationsSearchIndex::model()->deleteByPk($deleted_ids);
        }

        $this->redirect('/OphDrPrescription/refMedicationAdmin/list');

    }


}