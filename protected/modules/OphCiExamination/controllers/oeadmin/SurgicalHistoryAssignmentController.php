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

    namespace OEModule\OphCiExamination\controllers;

    use OEModule\OphCiExamination\models\SurgicalHistorySet;
    use OEModule\OphCiExamination\models\SurgicalHistorySetEntry;

    class SurgicalHistoryAssignmentController extends \ModuleAdminController
    {
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
            $model = new SurgicalHistorySet();
            $errors = null;

            if(\Yii::app()->request->isPostRequest) {
                $model->setAttributes(\Yii::app()->request->getPost('OEModule_OphCiExamination_models_SurgicalHistorySet'), false);
                /** @var \CDbTransaction $transaction */
                $transaction = \Yii::app()->db->beginTransaction();
                try {
                    $entries = SurgicalHistorySetEntry::model()->populateRecords(\Yii::app()->request->getPost('OEModule_OphCiExamination_models_SurgicalHistorySetEntry'));
                    $model->entries = $entries;
                    if($model->save()) {
                        $transaction->commit();
                        $this->redirect(array('index'));
                    }
                    else {
                        $transaction->rollback();
                        $errors = $model->getErrors();
                    }
                }
                catch (\Exception $e) {
                    \OELog::log($e->getMessage());
                    $transaction->rollback();
                }
            }

            $this->render('/admin/surgicalhistoryassignment/create',array(
                'model' => $model,
                'errors' => $errors
            ));
        }

        public function actionUpdate($id)
        {
            $model = $this->loadModel($id);
            $this->render('/admin/surgicalhistoryassignment/update',array(
                'model' => $model,
            ));
        }

        public function actionDelete()
        {
            $model_ids = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_SurgicalHistorySet', array());

            foreach($model_ids as $model_id){

                $model = $this->loadModel($model_id);
                if(!$model->entries){
                    $model->delete();
                } else {
                    echo "0";
                    \Yii::app()->end();
                }
            }

            //handleButton.js's handleButton($('#et_delete') function needs this return
            echo "1";
            \Yii::app()->end();
        }

        /**
         * @param $id
         * @return \CActiveRecord
         */

        private function loadModel($id)
        {
            $model = SurgicalHistorySet::model()->findByPk($id);
            if($model===null) {
                throw new \CHttpException(404,'The requested page does not exist.');
            }
            return $model;
        }
    }