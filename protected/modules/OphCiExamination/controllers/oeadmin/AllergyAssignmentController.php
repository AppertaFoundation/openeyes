<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
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

use OEModule\OphCiExamination\models;

class AllergyAssignmentController extends \ModuleAdminController
{
    public $group = 'Examination';

    public function accessRules()
    {
        return array(
            array('allow', 'users' => array('@')),
        );
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $model = new models\OphCiExaminationAllergySet();
        $model->unsetAttributes();
        if (isset($_GET['OphCiExaminationAllergy']))
            $model->attributes = $_GET['OphCiExaminationAllergy'];

        $this->render('/admin/allergyassignment/index', array(
            'model' => $model,
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $allergy_set = new models\OphCiExaminationAllergySet;

        if (isset($_POST['OEModule_OphCiExamination_models_OphCiExaminationAllergySet'])) {
            $allergy_set->attributes = $_POST['OEModule_OphCiExamination_models_OphCiExaminationAllergySet'];

            $transaction = \Yii::app()->db->beginTransaction();

            try {

                if ($allergy_set->save()) {

                    $allergies = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry', array());

                    foreach ($allergies as $allergy) {
                        $allergy_model = new models\OphCiExaminationAllergySetEntry;

                        $allergy_model->gender = $allergy['gender'] === "" ? null : $allergy['gender'];
                        $allergy_model->age_min = $allergy['age_min'] === "" ? null : $allergy['age_min'];
                        $allergy_model->age_max = $allergy['age_max'] === "" ? null : $allergy['age_max'];
                        $allergy_model->ophciexamination_allergy_id = $allergy['ophciexamination_allergy_id'];

                        if ($allergy_model->save()) {
                            $this->saveAssignment($allergy_set, $allergy_model);
                        }
                    }

                    $transaction->commit();

                    $this->redirect(array('index'));
                }
            } catch (\Exception $e) {
                \OELog::log($e->getMessage());
                $transaction->rollback();
            }

        }

        $this->render('/admin/allergyassignment/edit', array(
            'model' => $allergy_set,
            'title' => 'Create required allergy set',
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     * @throws CHttpException
     */
    public function actionUpdate($id)
    {
        $allergy_set = $this->loadModel($id);

        if (isset($_POST['OEModule_OphCiExamination_models_OphCiExaminationAllergySet'])) {
            $allergy_set->attributes = $_POST['OEModule_OphCiExamination_models_OphCiExaminationAllergySet'];

            $allergies = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExaminationAllergySetEntry', array());

            $transaction = \Yii::app()->db->beginTransaction();

            try {
                $posted_entry_ids = array();
                foreach ($allergies as $allergy) {
                    if (isset($allergy['id'])) {
                        $posted_entry_ids[] = $allergy['id'];
                    }
                }

                if ($allergy_set->save()) {
                    foreach ($allergies as $allergy) {
                        if (isset($allergy['id'])) {
                            $allergy_model = models\OphCiExaminationAllergySetEntry::model()->findByPk($allergy['id']);
                        } else {
                            $allergy_model = new models\OphCiExaminationAllergySetEntry;
                        }

                        $allergy_model->gender = $allergy['gender'] === "" ? null : $allergy['gender'];
                        $allergy_model->age_min = $allergy['age_min'] === "" ? null : $allergy['age_min'];
                        $allergy_model->age_max = $allergy['age_max'] === "" ? null : $allergy['age_max'];
                        $allergy_model->ophciexamination_allergy_id = $allergy['ophciexamination_allergy_id'];

                        if ($allergy_model->save()) {
                            $this->saveAssignment($allergy_set, $allergy_model);
                            $posted_entry_ids[] = $allergy_model->id;
                        }
                    }

                    // Removed items
                    $criteria = new \CDbCriteria();
                    $criteria->addCondition('allergy_set_id =:allergy_set_id');
                    $criteria->addNotInCondition('ophciexamination_allergy_entry_id', $posted_entry_ids);
                    $criteria->params[':allergy_set_id'] = $allergy_set->id;

                    $assignments = models\OphCiExaminationAllergySetAssignment::model()->findAll($criteria);
                    foreach ($assignments as $assignment) {
                        $entry_id = $assignment->ophciexamination_allergy_entry_id;

                        if ($assignment->delete()) {
                            models\OphCiExaminationAllergySetEntry::model()->findByPk($entry_id)->delete();
                        }
                    }
                }

                $transaction->commit();
                \Yii::app()->user->setFlash('success', 'Set updated.');
                $this->redirect(array('index'));

            } catch (\Exception $e) {
                \OELog::log($e->getMessage());
                $transaction->rollback();
                \Yii::app()->user->setFlash('error', 'Something went wrong. Set did not updated.');
            }
        }

        $this->render('/admin/allergyassignment/edit', array(
            'model' => $allergy_set,
            'title' => 'Edit required allergy set',
        ));
    }

    private function saveAssignment($allergy_set, $allergy_model)
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('allergy_set_id = :set_id');
        $criteria->addCondition('ophciexamination_allergy_entry_id = :ophciexamination_allergy_entry_id');
        $criteria->params[':set_id'] = $allergy_set->id;
        $criteria->params[':ophciexamination_allergy_entry_id'] = $allergy_model->id;

        $assignment = models\OphCiExaminationAllergySetAssignment::model()->find($criteria);

        if (!$assignment) {
            $assignment = new models\OphCiExaminationAllergySetAssignment;
            $assignment->ophciexamination_allergy_entry_id = $allergy_model->id;
            $assignment->allergy_set_id = $allergy_set->id;

            if (!$assignment->save()) {
                throw new \Exception('OphCiExaminationAllergy assignment cannot be saved.');
            }
        }
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete()
    {
        $model_ids = \Yii::app()->request->getPost('OEModule_OphCiExamination_models_OphCiExaminationAllergySet', array());

        foreach ($model_ids as $model_id) {
            $model = $this->loadModel($model_id);
            try {
                $model->delete();
            } catch (Exception $exception) {
                echo "0";
                \Yii::app()->end();
            }
        }

        //handleButton.js's handleButton($('#et_delete') function needs this return
        echo "1";
        \Yii::app()->end();
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return OphCiExaminationAllergy the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = models\OphCiExaminationAllergySet::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }
}