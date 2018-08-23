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
        $admin = new AdminListAutocomplete(RefMedicationSet::model(), $this);
        $admin->setListFields(array(
            'refMedication.id',
            'refMedication.source_type',
            'refMedication.preferred_term',
            'refMedication.vtm_term',
            'refMedication.vmp_term',
            'refMedication.amp_term',
            //'refSet.name'
        ));

        if($ref_set_id = Yii::app()->request->getParam('search')['filterid']['ref_set_id']['value']) {
            if(!$refSet = RefSet::model()->findByPk($ref_set_id)) {
                throw new CHttpException(404, 'Ref Set not found');
            }

            $admin->setModelDisplayName("Medication list for '".$refSet->name."' set");
        }
/*
        $admin->setFilterFields(
            array(
                array(
                    'label' => 'Ref Set',
                    'dropDownName' => 'ref_set_id',
                    'defaultValue' => null,//Yii::app()->session['selected_site_id'],
                    'listModel' => RefSet::model(),
                    'listIdField' => 'id',
                    'listDisplayField' => 'name',
                ),
            )
        );

  */

        $admin->setAutocompleteField(
            array(
                'fieldName' => 'refMedication.id',
                'jsonURL' => '/OphDrPrescription/default/DrugList',
                'placeholder' => 'search for medications',
            )
        );

        $admin->listModel();
    }


}