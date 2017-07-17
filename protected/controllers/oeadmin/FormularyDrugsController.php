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

/**
 * Created by PhpStorm.
 * User: himanshu
 * Date: 16/04/15
 * Time: 12:28.
 */

/**
 * Class FormularyDrugsController.
 */
class FormularyDrugsController extends BaseAdminController
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
     * Lists procedures.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $admin = new Admin(FormularyDrugs::model(), $this);
        $admin->setListFields(array(
            'name',
            'tagnames',
            'aliases',
            'active',
        ));
        $admin->getSearch()->addSearchItem('name, aliases');
        $admin->getSearch()->addSearchItem('tags.name');
        $admin->setModelDisplayName('Formulary Drugs');
        $admin->getSearch()->addActiveFilter();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);

        $criteria = new CDbCriteria();

        foreach (array('name', 'name, aliases', 'active') as $field)
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
            $command = Yii::app()->db->createCommand("SELECT drug_id FROM drug_tag WHERE tag_id IN (SELECT id FROM tag WHERE name LIKE CONCAT('%', :tagname ,'%'))");
            $matching_ids = $command->queryColumn(array(':tagname' => $_GET['search']['tags.name']));
            $criteria->addInCondition('id', $matching_ids, 'AND');
        }

        $admin->getSearch()->setCriteria($criteria);

        $admin->listModel();
    }

    /**
     * Edits or adds a Procedure.
     *
     * @param bool|int $id
     *
     * @throws CHttpException
     */
    public function actionEdit($id = false)
    {
        $admin = new Admin(FormularyDrugs::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setModelDisplayName('Formulary Drugs');
        $criteria = new CDbCriteria();
        $admin->setEditFields(array(
            'id' => 'label',
            'name' => 'text',
            'aliases' => 'text',
            'tallman' => 'text',
            'form_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(DrugForm::model()->findAll(), 'id', 'name'),
                'htmlOptions' => null,
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'dose_unit' => 'text',
            'default_dose' => 'text',
            'default_route_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(DrugRoute::model()->findAll(), 'id', 'name'),
                'htmlOptions' => array('empty' => '-- Please select --'),
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'default_frequency_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(DrugFrequency::model()->findAll(), 'id', 'name'),
                'htmlOptions' => array('empty' => '-- Please select --'),
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'default_duration_id' => array(
                'widget' => 'DropDownList',
                'options' => CHtml::listData(DrugDuration::model()->findAll(), 'id', 'name'),
                'htmlOptions' => array('empty' => '-- Please select --'),
                'hidden' => false,
                'layoutColumns' => null,
            ),
            'active' => 'checkbox',
            'allergy_warnings' => array(
                'widget' => 'MultiSelectList',
                'relation_field_id' => 'id',
                'label' => 'Allergy Warnings',
                'options' => CHtml::encodeArray(CHtml::listData(
                    Allergy::model()->findAll($criteria->condition = "name != 'Other'"),
                    'id',
                    'name'
                )),
            ),
            'tags' => array(
                'widget' => 'TagsInput',
                'relation' => 'tags',
                'relation_field_id' => 'id',
                'label' => 'Tags',
                /*'options' => CHtml::encodeArray(CHtml::listData(
                    Tag::model()->findAll(),
                    'id',
                    'name'
                )),*/
                'htmlOptions' => array(
                    'autocomplete_url' => $this->createAbsoluteUrl('/oeadmin/formularyDrugs/tagsAutocomplete')
                )
            ),
            'national_code' => 'text',
        ));
        $admin->editModel();
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $admin = new Admin(FormularyDrugs::model(), $this);
        $admin->deleteModel();
    }

    public function actionTagsAutocomplete($term)
    {
        $tags = Tag::model()->findAllBySql("SELECT * FROM tag WHERE name LIKE CONCAT('%', :term, '%')", array(':term'=>$term));
        $tnames = array();
        foreach ($tags as $tag)
        {
            $tnames[] = $tag->name;
        }

        header('content-type: application/json');
        echo CJSON::encode($tnames);
    }
}
