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

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RefMedicationAdminController extends BaseAdminController
{
    /*
       Filter that can be applied to items in order to
       create admin screens for specific subsets of drugs
    */
    public $group = 'Drugs';
    protected $source_type;
    protected $display_name;
    protected $list_mode_buttons = true;

    public function actionList()
    {
        $admin = new Admin(Medication::model(), $this);
        $admin->setListFields(array(
            'id',
            'source_type',
            'source_subtype',
            'preferred_code',
            'preferred_term',
            'alternativeTerms',
            'vtm_term',
            'vmp_term',
            'amp_term',
        ));

        if (!is_null($this->source_type)) {
            $admin->getSearch()->getCriteria()->addColumnCondition(['source_type' => $this->source_type]);
        }

        $admin->getSearch()->getCriteria()->addColumnCondition(['deleted_date' => null]);

        if (is_null($this->source_type)) {
            $admin->getSearch()->addSearchItem('source_type');
        }

        $admin->getSearch()->addSearchItem('source_subtype');
        $admin->getSearch()->addSearchItem('preferred_code');
        $admin->getSearch()->addSearchItem('preferred_term');

        $admin->setModelDisplayName($this->display_name);

        $admin->listModel($this->list_mode_buttons);
    }

    public function actionEdit($id = null)
    {
        if (is_null($id)) {
            $model = new Medication();
            $model->source_type = EventMedicationUse::USER_MEDICATION_SOURCE_TYPE;
            $model->source_subtype = EventMedicationUse::USER_MEDICATION_SOURCE_SUBTYPE;
            $model->preferred_code = Medication::getNextUnmappedPreferredCode();

            if (isset($this->source_type)) {
                $model->source_type = $this->source_type;
            }
        } else {
            if (!$model = Medication::model()->findByPk($id)) {
                throw new CHttpException(404);
            }
        }

        $this->_getEditAdmin($model)->editModel();
    }

    protected function _getEditFields($model)
    {
        return array(
            'preferred_term'=>'Preferred term',
            'short_term'=>'Short term',
            'preferred_code'=>'Preferred code',
            'source_type' => array(
                'widget' => 'DropDownList',
                'options' => $this->_getSourceTypes(),
                'htmlOptions' => array(
                    'empty' => '-- None --',
                    'class' => 'cols-full disabled',
                    'disabled' => 'disabled',
                ),
                'hidden' => false,
                'layoutColumns' => array()
            ),
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
            'vtm_term' => 'VTM term',
            'vtm_code' => 'VTM code',
            'vmp_term' => 'VMP term',
            'vmp_code' => 'VMP code',
            'amp_term' => 'AMP term',
            'amp_code' => 'AMP code',
            'default_dose' => 'Default dose',
            'default_dose_unit_term' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(MedicationAttributeOption::model()->with('medicationAttribute')->findAll(
                    ["condition" => "medicationAttribute.name = 'UNIT_OF_MEASURE'",
                        'order' => 'description asc']
                ), "description", "description"),
                'htmlOptions' => array('empty' => '-- None --', 'class' => 'cols-full'),
                'hidden' => false,
                'layoutColumns' => array()
            ),
            'default_form_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(MedicationForm::model()->findAll("deleted_date IS NULL"), "id", "term"),
                'htmlOptions' => array('empty' => '-- None --', 'class' => 'cols-full'),
                'hidden' => false,
                'layoutColumns' => array()
            ),
            'default_route_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(MedicationRoute::model()->findAll("deleted_date IS NULL"), "id", "term"),
                'htmlOptions' => array('empty' => '-- None --', 'class' => 'cols-full'),
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

    protected function _getEditAdmin(Medication $model)
    {
        $admin = new Admin($model, $this);

        $admin->setEditFields($this->_getEditFields($model));

        $admin->setModelDisplayName("Medication");
        $admin->setCustomSaveURL('/OphDrPrescription/OphDrPrescriptionAdmin/' . $this->id . '/save/' . $model->id);

        return $admin;
    }

    protected function _getSourceTypes()
    {
        $values = Yii::app()->db->createCommand("SELECT DISTINCT source_type FROM " . Medication::model()->tableName())->queryColumn();
        $ret_array = array();
        foreach ($values as $value) {
            $ret_array[$value] = $value;
        }
        return $ret_array;
    }

    protected function _getSourceSubtypes()
    {
        $values = Yii::app()->db->createCommand("SELECT DISTINCT source_subtype FROM " . Medication::model()->tableName())->queryColumn();
        $ret_array = array();
        foreach ($values as $value) {
            $ret_array[$value] = $value;
        }
        return $ret_array;
    }

    public function actionSave($id = null)
    {
        if (is_null($id)) {
            $model = new Medication();
            $model->source_type = EventMedicationUse::USER_MEDICATION_SOURCE_TYPE;
            $model->source_subtype = EventMedicationUse::USER_MEDICATION_SOURCE_SUBTYPE;
            $model->preferred_code = Medication::getNextUnmappedPreferredCode();
        } else {
            if (!$model = Medication::model()->findByPk($id)) {
                throw new CHttpException(404);
            }
        }

        /** @var CDbTransaction $trans */
        $transaction = Yii::app()->db->beginTransaction();

        /** @var Medication $model */

        $data = Yii::app()->request->getPost('Medication');
        $this->_setModelData($model, $data);

        if ($model->save()) {
            $transaction->commit();
        } else {
            $transaction->rollback();
            $admin = $this->_getEditAdmin($model);
            $this->render($admin->getEditTemplate(), array('admin' => $admin, 'errors' => $model->getErrors() ));
            exit;
        }

        $this->redirect('/'.$this->getModule()->id.'/'.$this->id.'/list');

    }

    private function _setModelData(Medication $model, $data)
    {
        $model->setAttributes($data);

        //Yii will set an empty string as the id but then won't update the model object's id if it's not null
        //So we need to set it to null for the model relations to save properly
        if(empty($model->id)) {
            $model->id = null;
        }

        if (!array_key_exists('source_type', $data)) {
            $model->source_type = $this->source_type;
        }

        $alt_terms = array();
        if (array_key_exists('medicationSearchIndexes', $data)) {
            foreach ($data['medicationSearchIndexes']['id'] as $key => $rid) {
                $alt_terms[] = [ 'alternative_term' => $data['medicationSearchIndexes']['alternative_term'][$key] ];
            }
        }

      // ensure that preferred_term exists as alternative term
        $pref_term_exists = false;
        foreach ($alt_terms as $si) {
            if ($si['alternative_term'] == $model->preferred_term) {
                $pref_term_exists = true;
                break;
            }
        }

        if (!$pref_term_exists) {
            $alt_terms[] = [ 'alternative_term' => $model->preferred_term ];
        }

        $model->medicationSearchIndexes = $alt_terms;


      // update attribute assignments
        $attr_assignments = [];
        if (array_key_exists('medicationAttributeAssignment', $data)) {
            foreach ($data['medicationAttributeAssignment']['id'] as $key => $assignment_id) {
                if ($assignment_id == -1) {
                    $assignment = new MedicationAttributeAssignment();
                    $assignment->medication_id = $model->id;
                    $assignment->medication_attribute_option_id = $data['medicationAttributeAssignment']['medication_attribute_option_id'][$key];
                } else {
                    $assignment = MedicationAttributeAssignment::model()->findByPk($assignment_id);
                    $assignment->medication_attribute_option_id = $data['medicationAttributeAssignment']['medication_attribute_option_id'][$key];
                }
                $attr_assignments[] = $assignment;
            }
        }
        $model->medicationAttributeAssignments = $attr_assignments;


      // update set memberships
        $medicationSetItems = array();
        if (array_key_exists('medicationSetItems', $data)) {
            foreach ($data['medicationSetItems'] as $attribute_key => $attribute_values) {
                if ($attribute_key != 'id') {
                    foreach ($attribute_values as $attribute_id => $attribute_value) {
                        if (!array_key_exists($attribute_id, $medicationSetItems)) {
                            $medicationSetItems[$attribute_id] = [];
                        }
                        $medicationSetItems[$attribute_id][$attribute_key] = $attribute_value;
                    }
                }
            }
        }

        $model->medicationSetItems = $medicationSetItems;
    }

    public function actionExportForm()
    {
        $data = array();

        if (Yii::app()->request->isPostRequest) {
            $med_set_ids = Yii::app()->request->getPost('MedicationSet');
            if (!empty($med_set_ids)) {
                return $this->actionExport($med_set_ids);
            } else {
                $data['form_error'] = 'Please select at least one Set.';
            }
        }
        return $this->render('/admin/ref_medication_export', $data);
    }

    public function actionExport($med_set_ids = array())
    {
        ini_set('max_execution_time', 0);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $style_bold = [
            'font' => [
                'bold' => true,
            ]
        ];

        $sheet->setCellValue('A4', 'DM+D ID (preffered code)');
        $sheet->setCellValue('B4', 'Term (preferred term');
        $sheet->setCellValue('C4', 'Source type');
        $sheet->setCellValue('D4', 'Source subtype');
        $sheet->setCellValue('E4', 'Notes');
        $sheet->getStyle('A4:E4')->applyFromArray($style_bold);

        $sheet->setCellValue('E1', 'SET TITLE >>');
        $sheet->setCellValue('E2', 'SET ID >>');
        $sheet->setCellValue('E3', 'SET INFO >>');

        $cond = new CDbCriteria();
        $cond->addInCondition('id', $med_set_ids);

        $sets = MedicationSet::model()->findAll($cond);

        $sets_array = [];

        $sets_array[0] = array_map(function ($e) {
            return $e->name;
        }, $sets);
        $sets_array[1] = array_map(function ($e) {
            return $e->id;
        }, $sets);
        $sets_array[2] = array_map(function ($e) {
            /** @var MedicationSet $e */
            $ruleString = [];
            foreach ($e->medicationSetRules as $rule) {
                $ruleString[] = ($rule->usageCode ? $rule->usageCode->usage_code : '') . " (site=" . (is_null($rule->site_id) ? "null" : $rule->site->name) . ", ss=" . (is_null($rule->subspecialty_id) ? "null" : $rule->subspecialty->name) . ")";
            }

            return implode(PHP_EOL, $ruleString);
        }, $sets);

        $sheet->fromArray($sets_array, null, 'E1');

        $cond = new CDbCriteria();
        $cond->addCondition("id IN (SELECT medication_id FROM medication_set_item WHERE medication_set_id IN (" . implode(",", $med_set_ids) . "))");

        $medications = Medication::model()->findAll($cond);

        $cells_array = [];

        foreach ($medications as $med) {
            $row = [
                $med->preferred_code, $med->preferred_term, $med->source_type, $med->source_subtype, ''
            ];

            foreach ($sets as $set) {
                if ($ref_medication_set = MedicationSetItem::model()->find("medication_id = {$med->id} AND medication_set_id = {$set->id}")) {
                    $repr = new stdClass();
                    $repr->dose = $ref_medication_set->default_dose;
                    $repr->dose_unit = $ref_medication_set->default_dose_unit_term;
                    $repr->route = is_null($ref_medication_set->default_route) ? null : $ref_medication_set->defaultRoute->term;
                    $repr->frequency = is_null($ref_medication_set->default_frequency) ? null : $ref_medication_set->defaultFrequency->term;
                    $repr->duration = is_null($ref_medication_set->default_duration) ? null : $ref_medication_set->defaultDuration->name;

                    if (!empty($ref_medication_set->tapers)) {
                        $repr->tapers = array();
                        foreach ($ref_medication_set->tapers as $taper) {
                            $t = new stdClass();
                            $t->dose = $taper->dose;
                            $t->frequency = is_null($taper->frequency_id) ? null : $taper->frequency->term;
                            $t->duration = is_null($taper->duration_id) ? null : $taper->duration->name;
                            $repr->tapers[] = $t;
                        }
                    }

                    $row[] = json_encode($repr);
                } else {
                    $row[] = null;
                }
            }
            $cells_array[] = $row;
        }

        $sheet->fromArray($cells_array, null, 'A5');

        $writer = new Xlsx($spreadsheet);
        $writer->save('/tmp/refMedExport.xlsx');

        Yii::app()->request->sendFile("refMedExport.xlsx", @file_get_contents('/tmp/refMedExport.xlsx'), 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', false);
    }

    public function actionDelete()
    {
        try {
            foreach (Yii::app()->request->getPost('Medication')['id'] as $id) {
                $medication = Medication::model()->findByPk($id);
                /** @var Medication $medication */
                foreach ($medication->medicationSearchIndexes as $index) {
                    $index->delete();
                }
                $medication->delete();
            }
        } catch (Exception $e) {
            echo "0";
            exit;
        }

        echo "1";
        exit;
    }
}
