<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class CommonSystemicDisorderController extends BaseAdminController
{
    public $group = 'Disorders';

    public function actionList()
    {
        $models = CommonSystemicDisorderGroup::model()->findAll();
        $data = array_map(function ($model) {
            return $model->getAttributes(array("id", "name"));
        }, $models);
        $this->jsVars['common_systemic_disorder_group_options'] = $data;
        Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->createUrl('js/OpenEyes.UI.DiagnosesSearch.js'), ClientScript::POS_END);

        $this->render('/admin/editcommonsystemicdisorder', [
            'dataProvider' => new CActiveDataProvider('CommonSystemicDisorder', ['pagination' => false, 'criteria' => ['order' => 'display_order']])]);
    }

    public function actionSave()
    {
        $transaction = Yii::app()->db->beginTransaction();
        $JSON_string = Yii::app()->request->getPost('CommonSystemicDisorder');

        $json_error = false;
        if (!$JSON_string || !array_key_exists('JSON_string', $JSON_string)) {
            $json_error = true;
        }
        $JSON = json_decode(str_replace("'", '"', $JSON_string['JSON_string']), true);
        if (json_last_error() != 0) {
            $json_error = true;
        }
        if (!$json_error) {
            $disorders = array_map(function ($entry) {
                return $entry['CommonSystemicDisorder'];
            }, $JSON);

            $ids = array();
            foreach ($disorders as $disorder) {
                $common_systemic_disorder = CommonSystemicDisorder::model()->findByPk($disorder['id']);
                if (!$common_systemic_disorder) {
                    $common_systemic_disorder = new CommonSystemicDisorder;
                }

                $common_systemic_disorder->group_id = $disorder['group_id'];
                $common_systemic_disorder->disorder_id = $disorder['disorder_id'];
                $common_systemic_disorder->display_order = $disorder['display_order'];

                if (!$common_systemic_disorder->save()) {
                    $errors[] = $common_systemic_disorder->getErrors();
                }

                $ids[$common_systemic_disorder->id] = $common_systemic_disorder->id;
            }

            if (empty($errors)) {
                //Delete items
                $criteria = new CDbCriteria();
                if ($ids) {
                    $criteria->addNotInCondition('id', array_map(function ($id) {
                        return $id;
                    }, $ids));
                }

                $to_delete = CommonSystemicDisorder::model()->findAll($criteria);
                foreach ($to_delete as $item) {
                    if (!$item->delete()) {
                        $errors[] = $item->getErrors();
                    }
                    Audit::add('admin', 'delete', $item->primaryKey, null, array(
                        'module' => (is_object($this->module)) ? $this->module->id : 'core',
                        'model' => CommonSystemicDisorder::getShortModelName(),
                    ));
                }

                $transaction->commit();

                Yii::app()->user->setFlash('success', 'List updated.');
            }

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    foreach ($error as $attribute => $error_array) {
                        $display_errors = '<strong>' . (new CommonOphthalmicDisorder())->getAttributeLabel($attribute) . ':</strong> ' . implode(', ', $error_array);
                        Yii::app()->user->setFlash('warning.failure-' . $attribute, $display_errors);
                    }
                }
                $transaction->rollback();
            }
            $this->redirect(Yii::app()->request->urlReferrer);
        }
    }
}
