<?php

    /**
     * OpenEyes
     *
     * (C) OpenEyes Foundation, 2018
     * This file is part of OpenEyes.
     * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
     * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
     * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
     * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
     * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
     * COPYING. If not, see <http://www.gnu.org/licenses/>.
     *
     * @package OpenEyes
     * @link http://www.openeyes.org.uk
     * @author OpenEyes <info@openeyes.org.uk>
     * @copyright Copyright (c) 2017, OpenEyes Foundation
     * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
     */

    namespace OEModule\OphCiExamination\controllers\oeadmin;

    use OEModule\OphCiExamination\models\SurgicalHistorySet;
    use OEModule\OphCiExamination\models\SurgicalHistorySetEntry;

    class SurgicalHistoryAssignmentController extends \ModuleAdminController
    {
        public $group = 'Examination';

        /**
         * @inheritdoc
         */
        public function accessRules()
        {
            return array(
                array('allow', 'users' => array('@')),
            );
        }

        public function actionIndex()
        {
            $model= new SurgicalHistorySet();
            $model->unsetAttributes();
            $this->render('/admin/surgicalhistoryassignment/index', array(
                'model' => $model,
            ));
        }

        public function actionCreate()
        {
            $errors = false;
            $model = new SurgicalHistorySet();

            if(\Yii::app()->request->isPostRequest) {
                $errors = $this->populateAndSaveModel($model);
            }

            $this->render('/admin/surgicalhistoryassignment/edit',array(
                'model' => $model,
                'errors' => $errors,
                'title' => 'Create Required Surgical History Set',
            ));
        }

        public function actionUpdate($id)
        {
            $errors = false;
            $model = $this->loadModel($id);

            if(\Yii::app()->request->isPostRequest) {
                $errors = $this->populateAndSaveModel($model);
            }

            $this->render('/admin/surgicalhistoryassignment/edit',array(
                'model' => $model,
                'errors' => $errors,
                'title' => 'Edit Required Surgical History Set',
            ));
        }

        public function actionDelete()
        {
            $model_ids = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_SurgicalHistorySet', array());

            foreach($model_ids as $model_id){
                $model = $this->loadModel($model_id);
                $model->delete();
            }

            //handleButton.js's handleButton($('#et_delete') function needs this return
            echo "1";
            \Yii::app()->end();
        }

        /**
         * @param $id
         * @return \CActiveRecord
         * @throws \CHttpException
         */
        private function loadModel($id)
        {
            $model = SurgicalHistorySet::model()->findByPk($id);
            if($model===null) {
                throw new \CHttpException(404,'The requested page does not exist.');
            }
            return $model;
        }

        /**
         * @param SurgicalHistorySet $model
         * @return bool Whether input passed validation
         * @throws \Exception
         */

        private function populateAndSaveModel(SurgicalHistorySet $model)
        {
            $errors = false;
            $surgical_history_entries = array();
            $model->setAttributes(\Yii::app()->request->getPost('OEModule_OphCiExamination_models_SurgicalHistorySet'), false);

            /** @var \CDbTransaction $transaction */
            $transaction = \Yii::app()->db->beginTransaction();

            if(!$model->isNewRecord) {
                foreach ($model->entries as $entry) {
                    $entry->delete();
                }
            }

            try {

                if(!$model->validate()) {
                    $errors = true;
                }

                $entries = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_SurgicalHistorySetEntry');
                if(empty($entries)) {
                    $model->addError('entries', "Please add at least one operation");
                    $errors = true;
                }
                else {
                    foreach ($entries as $entry) {
                        $e = new SurgicalHistorySetEntry();
                        $e->setAttributes($entry);

                        $surgical_history_entries[] = $e;

                        if(!$e->validate()) {
                            $errors = true;
                        }
                    }

                    $model->entries = $surgical_history_entries;
                }

                if(!$errors && $model->save()) {
                    foreach ($surgical_history_entries as $entry) {
                        $entry->surgical_history_set_id = $model->id;
                        $entry->save();
                    }

                    $transaction->commit();
                    $this->redirect(array('index'));

                }
                else {
                    $transaction->rollback();
                    $errors = true;
                }
            }

            catch (\Exception $e) {
                $errors = true;
                $transaction->rollback();
            }

            return $errors;
        }
    }