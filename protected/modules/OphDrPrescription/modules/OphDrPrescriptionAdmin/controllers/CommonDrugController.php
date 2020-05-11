<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2015
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2015, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class CommonDrugController extends BaseAdminController
{

    public $group = 'Drugs';

    public function actionList()
    {
        $admin = new AdminListAutocomplete(SiteSubspecialtyDrug::model(), $this);

        $admin->setListFields(array(
            'id',
            'drugs.tallmanlabel',
            'drugs.dose_unit',
        ));

        $admin->setCustomDeleteURL('/OphDrPrescription/admin/default/commondrugsdelete');
        $admin->setCustomSaveURL('/OphDrPrescription/admin/default/commondrugsadd');
        $admin->setModelDisplayName('Common Drugs List');
        $admin->setFilterFields(
            array(
                array(
                    'label' => 'Site',
                    'dropDownName' => 'site_id',
                    'defaultValue' => Yii::app()->session['selected_site_id'],
                    'listModel' => Site::model(),
                    'listIdField' => 'id',
                    'listDisplayField' => 'short_name',
                    ),
                array(
                    'label' => 'Subspecialty',
                    'dropDownName' => 'subspecialty_id',
                    'defaultValue' => Firm::model()->findByPk(Yii::app()->session['selected_firm_id'])->serviceSubspecialtyAssignment->subspecialty_id,
                    'listModel' => Subspecialty::model(),
                    'listIdField' => 'id',
                    'listDisplayField' => 'name',
                ),
            )
        );
        // we set default search options
        if ($this->request->getParam('search') == '') {
            $admin->getSearch()->initSearch(array(
                    'filterid' => array(
                            'site_id' => Yii::app()->session['selected_site_id'],
                            'subspecialty_id' => Firm::model()->findByPk(Yii::app()->session['selected_firm_id'])->serviceSubspecialtyAssignment->subspecialty_id,
                        ),
                ));
        }

        $admin->setAutocompleteField(
            array(
                'fieldName' => 'drug_id',
                'jsonURL' => '/OphDrPrescription/default/DrugList',
                'placeholder' => 'search for drugs',
            )
        );
        //$admin->searchAll();
        $admin->div_wrapper_class = 'cols-5';
        $admin->listModel();
    }
}
