<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class TagsAdminController extends BaseAdminController
{
    public $group = 'Drugs';

    public $itemsPerPage = 50;

    /**
     * Show the list of tags
     */
    public function actionList()
    {
        $admin = new Admin(Tag::model(), $this);
        $admin->setListFields(array(
            'name',
            'active'
        ));
        $admin->getSearch()->addSearchItem('name');
        $admin->getSearch()->addActiveFilter();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->div_wrapper_class = 'cols-3';
        $admin->listModel();
    }

    public function actionEdit($id = null)
    {
        $admin = new Admin(Tag::model(), $this);
        if (!is_null($id)) {
            $admin->setModelId($id);
        }

        $admin->setCustomSaveURL('/TagsAdmin/save');

        $admin->setEditFields(array(
            'name' => is_null($id) ? 'text' : 'label',
            'active' => 'checkbox',
            'drugs' => array(
                'widget' => 'CustomView',
                'viewName' => 'application.modules.OphDrPrescription.modules.OphDrPrescriptionAdmin.views.tag_druglist',
                'viewArguments' => array(
                    'items' => is_null($id) ? array() : Tag::model()->findByPk($id)->drugs
                )
            ),
            'medication_drugs' => array(
                'widget' => 'CustomView',
                'viewName' => 'application.modules.OphDrPrescription.modules.OphDrPrescriptionAdmin.views.tag_medication_druglist',
                'viewArguments' => array(
                    'items' => is_null($id) ? array() : Tag::model()->findByPk($id)->medication_drugs
                )
            )

        ));

        $admin->editModel();
    }

    public function actionSave()
    {
        $id = $_POST['Tag']['id'];

        if ($id === '') {
            $tag = new Tag();
            $is_new = true;
        } else {
            $tag = Tag::model()->findByPk($id);
            $is_new = false;
        }

        $tag->name = filter_var($_POST['Tag']['name'], FILTER_SANITIZE_STRING);
        $tag->active = $_POST['Tag']['active'] == '1';

        if ($tag->save()) {
            Yii::app()->user->setFlash('success', 'Tag ' . ($is_new ? 'created' : 'updated'));
            $this->redirect(array('/TagsAdmin/list'));
        } else {
            $errors = $tag->getErrors();
            $err_str = '';
            foreach ($errors as $field => $error_msg) {
                $err_str .= implode('<br/>', $error_msg) . '<br/>';
            }
            Yii::app()->user->setFlash('warning.alert', $err_str);
            $this->redirect(array('/TagsAdmin/edit/' . $id));
        }
    }

    public function actionDelete()
    {
        $admin = new Admin(Tag::model(), $this);
        $admin->deleteModel();
    }
}