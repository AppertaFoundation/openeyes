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
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class MedicationSetAutoRulesAdminController extends BaseAdminController
{
	public function actionList()
	{
		$admin = new Admin(MedicationSetAutoRule::model(), $this);
		$admin->setListFields(array(
			'name',
			'medicationSet.name',
			"medicationSet.itemsCount",
			'medicationSet.adminListAction'
		));

		$admin->getSearch()->setItemsPerPage(30);

		$admin->setModelDisplayName("Rules for automatic medication sets");
		$admin->setListTemplate('application.modules.OphDrPrescription.views.admin.auto_set_rule.list');
		$admin->listModel(true);
	}

	public function actionEdit($id = null)
	{
		$admin = $this->_getAdmin($id);
		$admin->editModel();
	}

	private function _getAdmin($id)
	{
		$admin = new Admin(MedicationSetAutoRule::model(), $this);

		$admin->setModelDisplayName("Auto Set Rule");
		if($id) {
			$admin->setModelId($id);
		}

		$admin->setEditFields(array(
			'name'=>'Name',
			'medication_set_id' =>  array(
				'widget' => 'DropDownList',
				'options' => CHtml::listData(MedicationSet::model()->findAll(['order' => 'name']), 'id', 'name'),
				'htmlOptions' => array('empty' => '-- Create new Set based on rule name --'),
				'hidden' => false,
				'layoutColumns' => null,
			),
			'hidden' => array(
				'widget' => 'DropDownList',
				'options' => array(0 => 'Visible set', 1 => 'Hidden/system set'),
				'label' => 'Set is a',
				'htmlOptions' => array(),
				'hidden' => false,
				'layoutColumns' => []
			),
			'medicationSetAutoRuleAttributes' => array(
				'widget' => 'CustomView',
				'viewName' => 'application.modules.OphDrPrescription.views.admin.auto_set_rule.edit_attributes',
				'viewArguments' => array(
					'medicationSetAutoRule' => $admin->getModel()
				)
			),
			'medicationSetAutoRuleSetMemberships' => array(
				'widget' => 'CustomView',
				'viewName' => 'application.modules.OphDrPrescription.views.admin.auto_set_rule.edit_set_membership',
				'viewArguments' => array(
					'medicationSetAutoRule' => $admin->getModel()
				)
			),
			'medicationSetAutoRuleMedications' => array(
				'widget' => 'CustomView',
				'viewName' => 'application.modules.OphDrPrescription.views.admin.auto_set_rule.edit_individual_medications',
				'viewArguments' => array(
					'medicationSetAutoRule' => $admin->getModel(),
				)
			)
		));

		$admin->setCustomSaveURL('/OphDrPrescription/medicationSetAutoRulesAdmin/save/'.$id);
		return $admin;
	}

	public function actionSave($id = -1)
	{
		if($id > 0) {
			$model = MedicationSetAutoRule::model()->findByPk($id);
		}
		else {
			$model = new MedicationSetAutoRule();
		}


		$data = Yii::app()->request->getPost('MedicationSetAutoRule');

		$model->name = $data['name'];
		$model->hidden = $data['hidden'];
		$model->attrs = array();
		if(isset($data['attribute']['id']) && !empty($data['attribute']['id'])) {
			foreach ($data['attribute']['id'] as $key=>$attr_id) {
				$model->attrs[] = array(
					'id' => $attr_id,
					'medication_attribute_option_id' => $data['attribute']['medication_attribute_option_id'][$key]
				);
			}
		}

		$model->sets = array();
		if(isset($data['sets']['id']) && !empty($data['sets']['id'])) {
			foreach ($data['sets']['id'] as $key=>$s_id) {
				$model->sets[] = array(
					'id' => $s_id,
					'medication_set_id' => $data['sets']['medication_set_id'][$key]
				);
			}
		}

		$model->meds = array();
		if(isset($data['medications']['id']) && !empty($data['medications']['id'])) {
			foreach ($data['medications']['id'] as $key=>$m_id) {
				$model->meds[] = array(
					'id' => $m_id,
					'medication_id' => $data['medications']['medication_id'][$key],
					'include_parent' => $data['medications']['include_parent'][$key],
					'include_children' => $data['medications']['include_children'][$key],
				);
			}
		}

		$trans = Yii::app()->db->beginTransaction();
		if(!$model->save(true)) {
			$trans->rollback();
			$admin = $this->_getAdmin($id);
			$admin->setModel($model);
			$admin->editModel();
		}
		else {
			$trans->commit();
			$this->redirect("/OphDrPrescription/medicationSetAutoRulesAdmin/list");
		}
	}

	public function actionPopulateAll()
	{
		exec("php ".Yii::app()->basePath."/yiic populateAutoMedicationSets &");
		Yii::app()->user->setFlash('success', "Rebuild process started at ".date('H:i').".");
		$this->redirect('/OphDrPrescription/medicationSetAutoRulesAdmin/list');
	}
}