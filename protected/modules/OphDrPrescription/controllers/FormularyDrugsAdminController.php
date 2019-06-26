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

class FormularyDrugsAdminController extends BaseAdminController
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
        $admin->getSearch()->getCriteria()->join = "INNER JOIN medication_set_rule AS medSetRule ON medSetRule.medication_set_id = t.id";
        $admin->getSearch()->getCriteria()->addCondition('medSetRule.usage_code = \'Formulary\'');

        $admin->setListFieldsAction('toList');

        $admin->setModelDisplayName("Formulary drugs");
        $admin->listModel(false);
    }

    public function actionToList($id)
    {
        echo '<pre>' . print_r("depricated", true) . '</pre>';
        die(__FILE__ . ' :: ' . __LINE__);
        //$this->redirect('/OphDrPrescription/refMedicationSetAdmin/list?ref_set_id='.$id);
    }
}