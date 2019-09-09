<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
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

namespace OEModule\OphCiExamination\modules\ExaminationAdmin\controllers;

class BaseAssignmentController extends \ModuleAdminController
{
    public $group = 'Examination';

    public function populateAndSaveModel($model)
    {
        $new_entries = [];

        $model->setAttributes($this->getSetPostData(), false);

        /** @var \CDbTransaction $transaction */
        $transaction = \Yii::app()->db->beginTransaction();

        if (!$model->isNewRecord) {
            foreach ($model->entries as $entry) {
                $entry->delete();
            }
        }

        try {
            $errors = !$model->validate();

            $entries = $this->getSetEntryPostData();
            if (empty($entries)) {
                $model->addError('entries', "Please add at least one entry");
                $errors = true;
            } else {
                foreach ($entries as $entry) {
                    $e = $this->getNewSetEntry();
                    $e->setAttributes($entry);

                    $new_entries[] = $e;
                    $errors = !$e->validate();
                }

                $model->entries = $new_entries;
            }

            if (!$errors && $model->save()) {
                foreach ($new_entries as $entry) {
                    $entry->set_id = $model->id;
                    //false: already validated, no need to do it again
                    $entry->save(false);
                }

                $transaction->commit();
                $this->redirect(['index']);
            } else {
                $transaction->rollback();
                $errors = true;
            }
        } catch (\Exception $e) {
            $errors = true;
            $transaction->rollback();
        }

        return $errors;
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @throws CDbException
     * @throws CHttpException
     */
    public function actionDelete()
    {
        $set_ids = \Yii::app()->request->getPost(str_replace('\\', '_', $this->set_model_name), []);

        $transaction = \Yii::app()->db->beginTransaction();
        try {
            foreach ($set_ids as $set_id) {
                $valid = true;

                $set = $this->loadModel($set_id);
                foreach ($set->entries as $entry) {
                    $valid = $valid && $entry->delete();
                }
                $valid = $valid && $set->delete();
            }

            if ($valid) {
                $transaction->commit();
                echo "1";
            } else {
                $transaction->rollback();
                echo "0";
            }
        } catch (\Exception $e) {
            \OELog::log($e->getMessage());
            echo "0";
        }

        //handleButton.js's handleButton($('#et_delete') function needs echo "1" or "0"
        \Yii::app()->end();
    }


    public function getSetPostData()
    {
        return \Yii::app()->request->getPost(str_replace('\\', '_', $this->set_model_name));
    }

    public function getSetEntryPostData()
    {
        return \Yii::app()->request->getPost(str_replace('\\', '_', $this->entry_model_name));
    }

    public function getNewSetEntry()
    {
        return new $this->entry_model_name();
    }
}