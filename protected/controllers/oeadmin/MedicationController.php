<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
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

    public $group = 'Drugs';

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
            'tags'
        ));

        $admin->getSearch()->addSearchItem('name, aliases');
        $admin->getSearch()->addSearchItem('external_code');
        $admin->getSearch()->addSearchItem('external_source');
        $admin->getSearch()->addSearchItem('tags.name');

        $criteria = new CDbCriteria();

        foreach (array('name, aliases', 'external_code', 'external_source') as $field)
        {
            if(isset($_GET['search'][$field]) && $_GET['search'][$field] != '')
            {
                if(strpos($field, ', ') === false)
                {
                    // Single column fields
                    $criteria->compare($field, $_GET['search'][$field], ($field != 'active'));
                }
                else
                {
                    // Combined fields
                    $crit2 = new CDbCriteria();
                    foreach (explode(', ', $field) as $column)
                    {
                        $crit2->compare($column, $_GET['search'][$field], true, 'OR');
                    }

                    $criteria->mergeWith($crit2, 'AND');
                }
            }
        }

        if(isset($_GET['search']['tags.name']) && $_GET['search']['tags.name'] != '')
        {
            $command = Yii::app()->db->createCommand("SELECT medication_drug_id FROM medication_drug_tag WHERE tag_id IN (SELECT id FROM tag WHERE name LIKE CONCAT('%', :tagname ,'%'))");
            $matching_ids = $command->queryColumn(array(':tagname' => $_GET['search']['tags.name']));
            $criteria->addInCondition('id', $matching_ids, 'AND');
        }

        $admin->getSearch()->setCriteria($criteria);

        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->div_wrapper_class = 'cols-7';
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
                    'autocomplete_url' => $this->createUrl('/oeadmin/drug/tagsAutocomplete')
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
