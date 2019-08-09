<?php

use OEModule\OphCiExamination\models\OphCiExaminationPupillaryAbnormalitySet;
use OEModule\OphCiExamination\modules\ExaminationAdmin\controllers\BaseAssignmentController;

class PupillaryAbnormalityAssignmentController extends BaseAssignmentController
{
    public $entry_model_name = 'OEModule\OphCiExamination\models\OphCiExaminationPupillaryAbnormalitySetEntry';
    public $set_model_name = 'OEModule\OphCiExamination\models\OphCiExaminationPupillaryAbnormalitySet';

    public function accessRules()
    {
        return [
            ['allow', 'users' => ['@']],
        ];
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $model = new OphCiExaminationPupillaryAbnormalitySet();
        $model->unsetAttributes();
        if (isset($_GET['OphCiExamination_PupillaryAbnormalities_Abnormality'])) {
            $model->attributes = $_GET['OphCiExamination_PupillaryAbnormalities_Abnormality'];
        }

        $this->render('/pupillaryabnormalityassignment/index', [
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
        $model = new OphCiExaminationPupillaryAbnormalitySet();

        if (\Yii::app()->request->isPostRequest) {
            $errors = $this->populateAndSaveModel($model);
        }

        $this->render('/pupillaryabnormalityassignment/edit', [
            'model' => $model,
            'errors' => $errors,
            'title' => 'Create required pupillary abnormality set',
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

        $this->render('/pupillaryabnormalityassignment/edit', [
            'errors' => isset($errors) ? $errors : '',
            'model' => $model,
            'title' => 'Edit required pupillary abnormality set',
        ]);
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return OphCiExamination_PupillaryAbnormalities_Abnormality the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model = OphCiExaminationPupillaryAbnormalitySet::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }
}