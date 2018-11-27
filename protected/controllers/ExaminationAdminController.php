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
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class ExaminationAdminController extends \ModuleAdminController
{
    public function populateAndSaveModel($model)
    {
        $errors = false;
        $new_entries = array();

        $model->setAttributes($this->getSetPostData(), false);

        /** @var \CDbTransaction $transaction */
        $transaction = \Yii::app()->db->beginTransaction();

        if (!$model->isNewRecord) {
            foreach ($model->entries as $entry) {
                $entry->delete();
            }
        }

        try {

            if (!$model->validate()) {
                $errors = true;
            }


            $entries = $this->getSetEntryPostData();
            if (empty($entries)) {
                $model->addError('entries', "Please add at least one entry");
                $errors = true;
            } else {
                foreach ($entries as $entry) {
                    $e = $this->getNewSetEntry();
                    $e->setAttributes($entry);

                    $new_entries[] = $e;

                    if (!$e->validate()) {
                        $errors = true;
                    }
                }

                $model->entries = $new_entries;
            }

            if (!$errors && $model->save()) {
                foreach ($new_entries as $entry) {
                    $entry->set_id = $model->id;
                    $entry->save();
                }

                $transaction->commit();
                $this->redirect(array('index'));

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