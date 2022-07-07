<?php

use OEModule\BreakGlass\BreakGlass;

class DefaultController extends BaseModuleController
{

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array(
                    'index',
                ),
                'roles' => array('User'),
            ),
        );
    }

    public function actionIndex()
    {
        // If value doesn't exist redirect to homepage
        if (!isset($_SESSION['breakglass_challengefor'])) {
            $this->redirect('/');
        }

        // Hide the sidebar.
        $this->fixedHotlist = false;
        $this->renderPatientPanel = false;

        $model = new BreakGlassModel();

        $id = $_SESSION['breakglass_challengefor'];
        $patient = Patient::model()->findByPk((int) $id);
        $current_user = Yii::app()->user;

        $break_glass = new BreakGlass($patient, $current_user);

        if (isset($_POST['BreakGlassModel'])) {
            $model->attributes = $_POST['BreakGlassModel'];
            $model->validate();

            if (!$model->hasErrors()) {
                $audit_data = $model->longreason ? $model->reason . ':' . $model->longreason : $model->reason;
                $patient->audit('BreakGlass', 'BreakGlassConfirmed', $audit_data, false, array('user_id' => Yii::app()->user->getId()));

                unset($_SESSION['breakglass_challengefor']);
                $_SESSION['breakglass_break_'.$id] = true;

                $api = new CoreAPI();
                $patient_page = $api->generatePatientLandingPageLink($patient);
                $this->redirect($patient_page);
            }
        } else {
            $patient->audit('BreakGlass', 'BreakGlassShown', null, false, array('user_id' => Yii::app()->user->getId()));
        }

        $patient_created_by = User::model()->findByPk($patient->created_user_id);
        $current_user = User::model()->findByPk(Yii::app()->user->getId());
        $institution = Institution::model()->getCurrent();
        $display_secondary_number_usage_code = SettingMetadata::model()->getSetting('display_secondary_number_usage_code');
        $secondary_identifier = PatientIdentifierHelper::getIdentifierForPatient($display_secondary_number_usage_code, $id, $institution->id);

        $this->layout = 'home';
        $this->render('index', array(
            'model' => $model,
            'patient' => $patient,
            'patient_identifier' => $secondary_identifier,
            'user' => $current_user,
            'patient_created_by' => $patient_created_by,
            'patient_hb' => $break_glass->patientHealthboard(),
            'user_hb' => $break_glass->userHealthboard(),
        ));
    }
}
