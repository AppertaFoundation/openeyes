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
class SnippetGroupController extends ModuleAdminController
{
    protected $admin;

    /**
     * @var int
     */
    public $itemsPerPage = 100;

    protected function beforeAction($action)
    {
        $this->admin = new Admin(LetterStringGroup::model(), $this);
        $this->admin->setModelDisplayName('Letter Snippet Group');

        return parent::beforeAction($action);
    }

    /**
     * Lists procedures.
     *
     * @throws CHttpException
     */
    public function actionList()
    {
        $this->admin->setListFields(array(
            'display_order',
            'id',
            'name',
        ));
        $this->admin->listModel();
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
        if ($id) {
            $this->admin->setModelId($id);
        }
        $this->admin->setEditFields(array(
            'name' => 'text',
            'siteLetterStrings' => array(
                'widget' => 'RelationList',
                'relation' => 'siteLetterStrings',
                'action' => 'OphCoCorrespondence/oeadmin/snippet',
                'search' => array('site_id' => array(
                    'type' => 'dropdown',
                    'options' => CHtml::listData(Institution::model()->getCurrent()->sites, 'id', 'short_name'),
                    'default' => Yii::app()->session['selected_site_id'],
                )),
                'listFields' => array(
                    'display_order',
                    'name',
                    'body',
                    'element_type.name',
                    'eventTypeName',
                ),
            ),
        ));
        $this->admin->editModel();
    }

    /**
     * Deletes rows for the model.
     */
    public function actionDelete()
    {
        $this->admin->deleteModel();
    }

    /**
     * Save ordering of the objects.
     */
    public function actionSort()
    {
        $this->admin->sortModel();
    }
}
