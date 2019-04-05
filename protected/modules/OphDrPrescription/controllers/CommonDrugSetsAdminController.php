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

class CommonDrugSetsAdminController extends BaseAdminController
{
	public $group = 'Drugs';

    public function actionList()
    {

        $admin = new Admin(MedicationSet::model(), $this);
        $admin->setListFields(array(
            'name',
            'itemsCount'
        ));

        $admin->getSearch()->setItemsPerPage(30);


        $default_site_id = Yii::app()->session['selected_site_id'];
        $default_subspecialty_id = Firm::model()->findByPk(Yii::app()->session['selected_firm_id'])->serviceSubspecialtyAssignment->subspecialty_id;


        /*
         * array('' => 'All') = add All option in search field (Name)
         * */
        $admin->getSearch()->addSearchItem('medicationSetRules.site_id', array(
            'type' => 'dropdown',
            'options' => array('' => 'All') + CHtml::listData(Site::model()->findAll(), 'id', 'name')

        ));

        $admin->getSearch()->addSearchItem('medicationSetRules.subspecialty_id', array(
            'type' => 'dropdown',
            'options' => array('' => 'All') + CHtml::listData(Subspecialty::model()->findAll(), 'id', 'name'),
        ));

        // we set default search options

        if ($this->request->getParam('search') == '') {
            $admin->getSearch()->initSearch(array(
                    'filterid' => array(
                        'medicationSetRules.site_id' => $default_site_id,
                        'medicationSetRules.subspecialty_id' => $default_subspecialty_id,
                        'medicationSetRules.usage_code' => 'COMMON_OPH'
                    ),
                )
            );
        }

        $admin->getSearch()->getCriteria()->addCondition('medicationSetRules.usage_code = \'COMMON_OPH\'');


        $admin->setListFieldsAction('toList');
        $admin->setEditFields('edit');

        $admin->setModelDisplayName("Common Drug Sets");
        $admin->listModel();
    }


    public function actionToList($id)
    {
        $this->redirect('/OphDrPrescription/refMedicationSetAdmin/list?ref_set_id='.$id);
    }

    public function actionEdit()
    {
        $this->redirect(['/OphDrPrescription/refSetAdmin/edit', 'usage_code' => 'COMMON_OPH']);
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