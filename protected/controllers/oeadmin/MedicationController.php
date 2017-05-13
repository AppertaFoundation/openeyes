<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
class MedicationController extends BaseAdminController
{
    /**
     * @var string
     */
    public $layout = 'admin';

    /**
     * @var int
     */
    public $itemsPerPage = 100;

    /**
     * Lists medications.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $admin = new Admin(MedicationDrug::model(), $this);
        $admin->setListFields(array(
            'id',
            'name',
            'external_code',
            'external_source',
            'aliases',
        ));
        $admin->searchAll();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }

    /**
     * Edits or adds medications.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $admin = new Admin(MedicationDrug::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setEditFields(array(
            'name' => 'text',
            'aliases' => 'text',
            'external_source' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(MedicationDrug::model()->findAll(new CDbCriteria(array('group' => 'external_source'))),
                    'external_source', 'external_source'),
                'htmlOptions' => null,
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'external_code' => 'text',
            'tags' => array(
                'widget' => 'TagsInput',
                'relation' => 'tags',
                'relation_field_id' => 'id',
                'label' => 'Tags',
                'htmlOptions' => array(
                    'autocomplete_url' => $this->createAbsoluteUrl('/oeadmin/formularyDrugs/tagsAutocomplete')
                )
            ),
        ));
        $admin->editModel();
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $admin = new Admin(MedicationDrug::model(), $this);
        $admin->deleteModel();
    }
}
