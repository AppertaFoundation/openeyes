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

class RefMedicationSetAdminController extends BaseAdminController
{
    public function actionList()
    {
        $ref_set_id = Yii::app()->request->getParam('ref_set_id');
        $medSet = MedicationSet::model()->findByPk($ref_set_id);

        $admin = new Admin(MedicationSetItem::model(), $this);
        $admin->setListFields(array(
            'medication.preferred_term',
        ));

        $admin->getSearch()->addSearchItem('medication.preferred_term');
        $admin->getSearch()->setItemsPerPage(30);
        $crit = new CDbCriteria();
        $crit->order = 'id ASC';
        $crit->condition = 'medication_set_id = '.$medSet->id;
        $admin->getSearch()->setCriteria($crit);

        $admin->setIsSubList(true);
        $admin->setSubListParent(array(
            'medication_set_id' => $medSet->id
        ));

        $admin->setModelDisplayName("Medications in set '{$medSet->name}'");
        $admin->setForceTitleDisplay(true);
        $admin->setForceFormDisplay(true);

        $admin->setListFieldsAction('medEditRedir');

        $admin->listModel();
    }

    public function actionMedEditRedir($id)
    {
        $ref_med_id = MedicationSetItem::model()->findByPk($id)->medication_id;
        $this->redirect('/OphDrPrescription/RefMedicationAdmin/edit/'.$ref_med_id);
    }

    public function actionEdit($id = null)
    {
        $admin = new Admin(MedicationSetItem::model(), $this);

        $ref_set_id =  Yii::app()->request->getParam('default')['medication_set_id'];
        $medSet = MedicationSet::model()->findByPk($ref_set_id);

        $admin->setEditFields(array(
            'medication_id'=> array(
                'widget' => 'RefMedicationLookup',
                'label' => 'Medication',
                'options' => array(
                    'hiddenFieldName' => 'MedicationSetItem[medication_id]',
                    'markupAfter' => '<br/>'
                )
            ),
            'medication_set_id' => 'hidden'
        ));
        $admin->setModelDisplayName("medication to set '{$medSet->name}'");
        if ($id) {
            $admin->setModelId($id);
        }

        $admin->editModel();
    }

    public function actionDelete()
    {
        $ids_to_delete = Yii::app()->request->getPost('MedicationSetItem')['id'];
        if (is_array($ids_to_delete)) {
            foreach ($ids_to_delete as $id) {
                $model = MedicationSetItem::model()->findByPk($id);
                $model->delete();
            }
        }

        exit("1");
    }
}
