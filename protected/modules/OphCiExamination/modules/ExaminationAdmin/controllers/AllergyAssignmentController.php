<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
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
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models\OphCiExaminationAllergySet;
use OEModule\OphCiExamination\modules\ExaminationAdmin\controllers\BaseAssignmentController;

class AllergyAssignmentController extends BaseAssignmentController
{
    public $entry_model_name = 'OEModule\OphCiExamination\models\OphCiExaminationAllergySetEntry';
    public $set_model_name = 'OEModule\OphCiExamination\models\OphCiExaminationAllergySet';

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $model = new OphCiExaminationAllergySet();
        $model->unsetAttributes();
        if (isset($_GET['OphCiExaminationAllergy'])) {
            $model->attributes = $_GET['OphCiExaminationAllergy'];
        }

        $this->render('/allergyassignment/index', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $errors = false;
        $model = new OphCiExaminationAllergySet();

        if (\Yii::app()->request->isPostRequest) {
            $errors = $this->populateAndSaveModel($model);
        }

        $this->render('/allergyassignment/edit', [
            'model' => $model,
            'errors' => $errors,
            'title' => 'Create required allergy set',
        ]);
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     * @throws CHttpException
     */
    public function actionUpdate($id)
    {
        $errors = false;
        $model = $this->loadModel($id);

        if (\Yii::app()->request->isPostRequest) {
            $errors = $this->populateAndSaveModel($model);
        }

        $this->render('/allergyassignment/edit', [
            'errors' => isset($errors) ? $errors : '',
            'model' => $model,
            'title' => 'Edit required allergy set',
        ]);
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
        $model = OphCiExaminationAllergySet::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }
}
