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

/**
 * Class ProceduresController.
 */
class ProcedureController extends BaseAdminController
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
        $admin = new Admin(Procedure::model(), $this);
        $admin->setListFields(array(
                            'term',
                            'snomed_code',
                            'opcsCodes.name',
                            'default_duration',
                            'aliases',
                            'has_benefits',
                            'has_complications',
                            'active',
        ));
        $admin->searchAll();
        $admin->getSearch()->addActiveFilter();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
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
        $admin = new Admin(Procedure::model(), $this);
        if ($id) {
            $admin->setModelId($id);
        }
        $admin->setEditFields(array(
            'term' => 'text',
            'short_format' => 'text',
            'default_duration' => 'text',
            'snomed_code' => 'text',
            'snomed_term' => 'text',
            'aliases' => 'text',
            'unbooked' => 'checkbox',
            'active' => 'checkbox',
            'opcsCodes' => array(
                'widget' => 'MultiSelectList',
                'relation_field_id' => 'id',
                'label' => 'OPCS Code',
                'options' => CHtml::encodeArray(CHtml::listData(
                    OPCSCode::model()->findAll(),
                    'id',
                    function ($model) {
                        return $model->name.': '.$model->description;
                    }
                )),
            ),
            'benefits' => array(
                'widget' => 'MultiSelectList',
                'relation_field_id' => 'id',
                'label' => 'Benefit',
                'options' => CHtml::encodeArray(CHtml::listData(
                    Benefit::model()->findAll(),
                    'id',
                    'name'
                )),
            ),
            'complications' => array(
                'widget' => 'MultiSelectList',
                'relation_field_id' => 'id',
                'label' => 'Complication',
                'options' => CHtml::encodeArray(CHtml::listData(
                    Complication::model()->findAll(),
                    'id',
                    'name'
                )),
            ),
        ));
        if (isset($admin->getModel()->operationNotes)) {
            $admin->setEditFields(array_merge(
                $admin->getEditFields(),
                array(
                    'operationNotes' => array(
                        'widget' => 'MultiSelectList',
                        'relation_field_id' => 'id',
                        'label' => 'Operation Note Element',
                        'options' => CHtml::encodeArray(CHtml::listData(
                            ElementType::model()->findAllByAttributes(array(), 'event_type_id in (select id from event_type where name = "Operation Note")'),
                            'id',
                            'name'
                        )),
                    ),
                )
            ));
        }
        $admin->editModel();
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $admin = new Admin(Procedure::model(), $this);
        $admin->deleteModel();
    }
}
