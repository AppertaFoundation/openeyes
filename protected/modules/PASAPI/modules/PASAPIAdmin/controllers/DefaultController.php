<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PASAPI\PASAPIAdmin\controllers;

use OEModule\PASAPI\models\XpathRemap;
use OEModule\PASAPI\models\RemapValue;

class DefaultController extends \BaseAdminController
{
    public $group = 'PASAPI';

    public function actionViewXpathRemaps()
    {
        \Audit::add('admin', 'list', null, false, array('module' => 'PASAPI', 'model' => 'OEModule\PASAPI\models\XpathRemap'));

        $this->render('list_XpathRemap', array(
            'model_class' => 'XpathRemap',
            'model_list' => XpathRemap::model()->findAll(array('condition' => 'institution_id='. \Yii::app()->session['selected_institution_id'], 'order' => 'name asc')),
            'title' => 'Remaps',
        ));
    }

    public function actionCreateXpathRemap()
    {
        $model = new XpathRemap();

        if (isset($_POST[\CHtml::modelName($model)])) {
            $model->attributes = $_POST[\CHtml::modelName($model)];

            if ($model->save()) {
                \Audit::add('admin', 'create', serialize($model->attributes), false, array('module' => 'PASAPI', 'model' => '\OEModule\PASAPI\models\XpathRemap'));
                \Yii::app()->user->setFlash('success', 'Xpath Remap '.$model->name.' added');

                $this->redirect(array('viewXpathRemaps'));
            }
        }

        $this->render('update', array(
            'model' => $model,
            'title' => 'Add Xpath Remap',
            'cancel_uri' => '/PASAPI/admin/default/viewXpathRemaps',
            'title' => 'Create Xpath'
        ));
    }

    public function actionUpdateXpathRemap($id)
    {
        if (!$model = XpathRemap::model()->findByPk($id)) {
            throw new \CHttpException('404', 'Could not Xpath Remap');
        }

        if (isset($_POST[\CHtml::modelName($model)])) {
            $model->attributes = $_POST[\CHtml::modelName($model)];

            if ($model->save()) {
                \Audit::add('admin', 'update', serialize($model->attributes), false, array('module' => 'PASAPI', 'model' => '\OEModule\PASAPI\models\XpathRemap'));
                \Yii::app()->user->setFlash('success', 'Xpath Remap "'.$model->name.'" updated');

                $this->redirect(array('viewXpathRemaps'));
            }
        }

        $this->render('update', array(
            'model' => $model,
            'title' => 'Update Xpath Remap',
            'cancel_uri' => '/PASAPI/admin/default/viewXpathRemaps',
            'title' => 'Update Xpath'
        ));
    }

    public function actionDeleteXpathRemap($id)
    {
        if (!$model = XpathRemap::model()->findByPk($id)) {
            throw new \CHttpException('404', 'Could not Xpath Remap');
        }

        $model->delete();

        \Audit::add('admin', 'update', serialize($model->attributes), false, array('module' => 'PASAPI', 'model' => '\OEModule\PASAPI\models\XpathRemap'));
        \Yii::app()->user->setFlash('success', 'Xpath Remap "'.$model->name.'" deleted');

        $this->redirect(array('viewXpathRemaps'));
    }

    public function actionViewRemapValues($id)
    {
        if (!$remap = XpathRemap::model()->findByPk($id)) {
            throw new \CHttpException('404', 'Could not Xpath Remap');
        }

        \Audit::add('admin', 'list', null, false, array('module' => 'PASAPI', 'model' => 'OEModule\PASAPI\models\RemapValue'));

        $this->render('list_RemapValue', array(
            'remap' => $remap,
            'model_class' => 'RemapValue',
            'model_list' => $remap->values,
            'title' => 'Remap Values for '.$remap->name,
        ));
    }

    public function actionCreateRemapValue($id)
    {
        if (!$remap = XpathRemap::model()->findByPk($id)) {
            throw new \CHttpException('404', 'Could not Xpath Remap');
        }

        $model = new RemapValue();
        $model->xpath_id = $remap->id;

        if (isset($_POST[\CHtml::modelName($model)])) {
            $model->attributes = $_POST[\CHtml::modelName($model)];

            if ($model->save()) {
                \Audit::add('admin', 'create', serialize($model->attributes), false, array('module' => 'PASAPI', 'model' => '\OEModule\PASAPI\models\RemapValue'));
                \Yii::app()->user->setFlash('success', 'Remap Value added to '.$remap->name);

                $this->redirect(array('viewRemapValues', 'id' => $id));
            }
        }

        $this->render('update', array(
            'model' => $model,
            'title' => 'Add Xpath Remap',
            'cancel_uri' => \Yii::app()->createUrl($this->module->getName().'/admin/viewRemapValues', array('id' => $remap->id)),
        ));
    }

    public function actionUpdateRemapValue($id)
    {
        if (!$model = RemapValue::model()->findByPk($id)) {
            throw new \CHttpException('404', 'Could not Remap Value');
        }

        if (isset($_POST[\CHtml::modelName($model)])) {
            $model->attributes = $_POST[\CHtml::modelName($model)];

            if ($model->save()) {
                \Audit::add('admin', 'update', serialize($model->attributes), false, array('module' => 'PASAPI', 'model' => '\OEModule\PASAPI\models\RemapValue'));
                \Yii::app()->user->setFlash('success', 'Remap Vlaue for "'.$model->xpath->name.'" updated');

                $this->redirect(array('viewRemapValues', 'id' => $id));
            }
        }

        $this->render('update', array(
            'model' => $model,
            'title' => 'Update Xpath Remap',
            'cancel_uri' => \Yii::app()->createUrl($this->module->getName().'/admin/viewRemapValues', array('id' => $model->xpath->id)),
        ));
    }

    public function actionDeleteRemapValue($id)
    {
        if (!$model = RemapValue::model()->findByPk($id)) {
            throw new \CHttpException('404', 'Could not Xpath Remap');
        }

        $model->delete();

        \Audit::add('admin', 'update', serialize($model->attributes), false, array('module' => 'PASAPI', 'model' => '\OEModule\PASAPI\models\RemapValue'));
        \Yii::app()->user->setFlash('success', 'Remap Value "'.$model->input.'" for "'.$model->xpath->name.'" deleted');

        $this->redirect(array('viewRemapValues', 'id' => $model->xpath_id));
    }
}
