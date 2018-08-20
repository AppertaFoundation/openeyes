<?php

/**
 * Class TrialPatientController
 */
class TrialPatientController extends BaseModuleController
{
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array('changeStatus', 'updateExternalId', 'updateTreatmentType'),
                'expression' => 'TrialPatient::checkTrialEditAccess($user, Yii::app()->getRequest()->getPost("id"), UserTrialAssignment::PERMISSION_EDIT)',
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param int $id the ID of the model to be loaded
     * @return TrialPatient the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        /* @var TrialPatient $model */
        $model = TrialPatient::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param TrialPatient $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'trial-patient-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    /**
     * Changes the status of a patient in a trial to a given value
     * @throws Exception Thrown the model cannot be saved
     */
    public function actionChangeStatus()
    {
        $trialPatient = $this->loadModel($_POST['id']);
        $result = $trialPatient->changeStatus($_POST['new_status']);
        echo $result;
    }

    /**
     * Changes the external_trial_identifier of a TrialPatient record
     *
     * @throws Exception Thrown if an error occurs when saving the model or if it cannot be found
     */
    public function actionUpdateExternalId()
    {
        $model = $this->loadModel($_POST['id']);
        $model->updateExternalId($_POST['new_external_id']);
    }

    /**
     * Updates the treatment type of a trial-patient with a new treatment type
     *
     * @throws Exception Thrown if an error occurs when saving the TrialPatient
     */
    public function actionUpdateTreatmentType()
    {
        $model = $this->loadModel($_POST['id']);
        $model->updateTreatmentType($_POST['treatment_type']);
    }
}
