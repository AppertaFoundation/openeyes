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

class RefSetAdminController extends BaseAdminController
{
	public $group = 'Drugs';

	public function actionList()
	{
		$admin = new Admin(MedicationSet::model(), $this);
		$admin->setListFields(array(
			'id',
			'name',
			'rulesString',
			'itemsCount',
			'hiddenString',
			'adminListAction'
		));

		$admin->getSearch()->addSearchItem('name');
		$admin->getSearch()->setItemsPerPage(30);
		$admin->getSearch()->getCriteria()->order = 'name ASC';

		$admin->setListFieldsAction('edit');


		$admin->setModelDisplayName("Medication sets");
		$admin->listModel();
	}

	public function actionToList($id)
	{
		$this->redirect('/OphDrPrescription/refMedicationAdmin/list?ref_set_id='.$id);
	}

	public function actionEdit($id = null)
	{
		$admin = new Admin(MedicationSet::model(), $this);

		$admin->setEditFields(array(
			'name'=>'Name',
			'rules' => array(
				'widget' => 'CustomView',
				'viewName' => 'application.modules.OphDrPrescription.views.admin.medication_set.edit_rules',
				'viewArguments' => array(
					'medication_set' => !is_null($id) ? MedicationSet::model()->findByPk($id) : new MedicationSet()
				)
			),

		));
		$admin->setModelDisplayName("Medication set");
		if($id) {
			$admin->setModelId($id);
		}
		$admin->setCustomSaveURL('/OphDrPrescription/refSetAdmin/save/'.$id);

		$admin->editModel();
	}

	public function actionSave($id = null)
	{
		if(is_null($id)) {
			$model = new MedicationSet();
		}
		else {
			if(!$model = MedicationSet::model()->findByPk($id)) {
				throw new CHttpException(404, 'Page not found');
			}
		}

		/** @var MedicationSet $model */

		$data = Yii::app()->request->getPost('MedicationSet');
		$model->setAttributes($data);

		$model->save();

		$existing_ids = array();
		$updated_ids = array();
		foreach ($model->medicationSetRules as $rule) {
			$existing_ids[] = $rule->id;
		}

		$medication_set = Yii::app()->request->getPost('MedicationSet');
		$ids = isset($medication_set['medicationSetRules']['id']) ? $medication_set['medicationSetRules']['id'] : [];

        foreach ($ids as $key => $rid) {
            if($rid == -1) {
                $medSetRule = new MedicationSetRule();
            }
            else {
                $medSetRule = MedicationSetRule::model()->findByPk($rid);
                $updated_ids[] = $rid;
            }

            $medSetRule->setAttributes(array(
                'medication_set_id' => $model->id,
                'site_id' => isset($medication_set['medicationSetRules']['site_id'][$key]) ? $medication_set['medicationSetRules']['site_id'][$key] : null,
                'subspecialty_id' => isset($medication_set['medicationSetRules']['subspecialty_id'][$key]) ? $medication_set['medicationSetRules']['subspecialty_id'][$key] : null,
                'usage_code_id' => isset($medication_set['medicationSetRules']['usage_code_id'][$key]) ? $medication_set['medicationSetRules']['usage_code_id'][$key] : null,
            ));

            $medSetRule->save();
        }


		$deleted_ids = array_diff($existing_ids, $updated_ids);
		if(!empty($deleted_ids)) {
			MedicationSetRule::model()->deleteByPk($deleted_ids);
		}

		$this->redirect('/OphDrPrescription/refSetAdmin/list');
	}

	public function actionDelete()
	{
		$ids_to_delete = Yii::app()->request->getPost('MedicationSet')['id'];
		if(is_array($ids_to_delete)) {
			foreach ($ids_to_delete as $id) {
				$model = MedicationSet::model()->findByPk($id);
				/** @var MedicationSet $model */
				foreach ($model->medicationSetRules as $rule) {
					$rule->delete();
				}
				foreach ($model->items as $i) {
					$i->delete();
				}
				$model->delete();
			}
		}

		exit("1");
	}
}