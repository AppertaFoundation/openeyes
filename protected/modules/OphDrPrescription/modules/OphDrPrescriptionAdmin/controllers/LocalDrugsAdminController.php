<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class LocalDrugsAdminController extends RefMedicationAdminController
{
    protected $source_type = "LOCAL";
    protected $display_name = "Local drugs";

    protected function _getEditFields($model)
    {
        return array(
            'preferred_term'=>'Preferred term',
            'short_term'=>'Short term',
            //'preferred_code'=>'Preferred code',
            'source_subtype'=> array(
                'widget' => 'DropDownList',
                'options' => $this->_getSourceSubtypes(),
        'htmlOptions' => array(
          'empty' => '-- None --',
          'class' => 'cols-full disabled',
          'disabled' => 'disabled',
            ),
                'hidden' => false,
                'layoutColumns' => array()
            ),
            'attributes' => array(
                'widget' => 'CustomView',
                'viewName' => 'application.modules.OphDrPrescription.views.admin.medication.edit_attributes',
                'viewArguments' => array(
                    'medication' => $model
                )
            ),
            'sets' => array(
                'widget' => 'CustomView',
                'viewName' => 'application.modules.OphDrPrescription.views.admin.medication.edit_sets',
                'viewArguments' => array(
                    'medication' => $model
                )
            ),
            'alternative_terms' =>  array(
                'widget' => 'CustomView',
                'viewName' => 'application.modules.OphDrPrescription.views.admin.medication.edit_alternative_terms',
                'viewArguments' => array(
                    'medication' => $model
                )
            ),
        );
    }

    public function actionListLocalDrugInstitutionMappings()
    {
        //This explodes the entire machine. be careful.
        $this->render('application.modules.OphDrPrescription.views.admin.medication.edit_local_drug_institution_mappings', array(
            'dataProvider' => new CActiveDataProvider('Medication', array(
                'criteria' => array(
                    'condition'=>'source_type="LOCAL"',
                    'order'=>'preferred_term ASC',
                ),
                'countCriteria'=>array(
                    'condition'=>'source_type="LOCAL"',
                ),
                'pagination' => array('pagesize' => 20),
            )),
        ));
    }

    public function actionEditLocalDrugInstitutionMappings()
    {
        $level = ReferenceData::LEVEL_INSTITUTION;
        $institution_id = Institution::model()->getCurrent()->id;

        $row_ids = Yii::app()->request->getPost('id');
        $selected = Yii::app()->request->getPost('selected');
        $transaction = Yii::app()->db->beginTransaction();
        $errors = array();
        try {
            foreach ($row_ids as $row => $id) {
                $record = Medication::model()->findByPk($id);

                $needs_mapping = isset($selected[$row]) && $selected[$row] == 1;

                if ($record->hasMapping(ReferenceData::LEVEL_INSTITUTION, $institution_id)) {
                    if (!$needs_mapping) {
                        $record->deleteMapping(ReferenceData::LEVEL_INSTITUTION, $institution_id);
                    }
                } else {
                    if ($needs_mapping) {
                        $record->createMapping(ReferenceData::LEVEL_INSTITUTION, $institution_id);
                    }
                }
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }

        if (!empty($errors)) {
            $transaction->rollback();
        } else {
            $transaction->commit();
        }
        $this->redirect('/OphDrPrescription/OphDrPrescriptionAdmin/localDrugsAdmin/ListLocalDrugInstitutionMappings');
    }
}
