<?php

class PSDController extends DefaultController
{
    protected static $action_types = array(
        'getSetMedications' => self::ACTION_TYPE_FORM,
        'getMedication' => self::ACTION_TYPE_FORM,
        'getPathStep' => self::ACTION_TYPE_FORM,
        'unlockPSD' => self::ACTION_TYPE_FORM,
        'confirmAdministration' => self::ACTION_TYPE_FORM,
        'checkPincode' => self::ACTION_TYPE_FORM,
    );

    protected $api;

    public function beforeAction($action)
    {
        $this->api = \Yii::app()->moduleAPI->get('OphDrPGDPSD');
        return parent::beforeAction($action);
    }

    /**
     * @param $partial              to display overview of the popup or full (with actions, detailed info)
     * @param $pathstep_id          for PathwayStep
     * @param $assignment_id        for OphDrPGDPSD_Assignment
     * @param $patient_id           for Patient
     * @param $for_administer       to display form or read only content
     * @param bool $interactive     to display popup buttons
     * @throws CHttpException
     * @throws Exception
     */
    public function actionGetPathStep($partial, $pathstep_id, $patient_id, $for_administer, $interactive = true)
    {
        $step = PathwayStep::model()->findByPk($pathstep_id);

        if (!$step) {
            throw new CHttpException(404, 'Unable to retrieve path step.');
        }

        $assignment_id = $step->getState('assignment_id');

        if (!$assignment_id) {
            throw new CHttpException(404, 'Unable to retrieve PSD id.');
        }
        $assignment = OphDrPGDPSD_Assignment::model()->find('id = :id AND active = 1', [':id' => $assignment_id]);

        if (!$assignment) {
            throw new CHttpException(404, 'Unable to retrieve PSD.');
        }

        $pathway = $step->pathway;

        if (!$pathway) {
            throw new CHttpException(404, 'Unable to retrieve Pathway.');
        }

        $pathway->updateStatus();

        $can_remove_psd = \Yii::app()->user->checkAccess('Prescribe')
            && (int)$step->status === PathwayStep::STEP_REQUESTED
            && !$assignment->elements ? '' : 'disabled';
        if ($interactive) {
            $interactive = in_array((int)$step->pathway->status, Pathway::inProgressStatuses(), true);
        }
        $dom = $this->renderPartial(
            '/pathstep/pathstep_view',
            array(
                'assignment' => $assignment,
                'step' => $step,
                'partial' => (int)$partial,
                'patient_id' => $patient_id,
                'for_administer' => $for_administer,
                'is_prescriber' => Yii::app()->user->checkAccess('Prescribe'),
                'can_remove_psd' => $can_remove_psd,
                'interactive' => (bool)$interactive,
            ),
            true
        );
        $ret = array(
            'dom' => $dom,
            'step' => $step->toJSON(),
            'pathway_status' => $pathway->getStatusString(),
            'status_html' => $pathway->getPathwayStatusHTML(),
            'step_html' => $this->renderPartial('//worklist/_clinical_pathway', ['pathway' => $pathway], true),
            'waiting_time_html' => $pathway->getTotalDurationHTML(true),
            'wait_time_details' => json_encode($pathway->getWaitTimeSinceLastAction()),
        );
        $this->renderJSON($ret);
    }

    public function actionCheckPincode()
    {
        $pincode = \Yii::app()->request->getParam('pincode', null);

        $assignment_id = Yii::app()->request->getParam('assignment_id', null);
        $assignment = \OphDrPGDPSD_Assignment::model()->findByPk($assignment_id);

        $ret = array(
            'success' => 0,
            'payload' => null,
        );

        if (!$assignment) {
            $this->renderJSON($ret);
            Yii::app()->end();
        }

        // current institution
        $current_institution_id = Yii::app()->session->get('selected_institution_id');
        $criteria = new CDbCriteria();
        $criteria->with = [
            'pincode',
            'authentications',
            'authentications.institutionAuthentication',
        ];
        // make sure the pincode is targetting active users who are in the current institution
        $criteria->compare('authentications.active', true);
        $criteria->compare('institutionAuthentication.institution_id', $current_institution_id);
        $criteria->compare('pincode.pincode', $pincode);
        $user = User::model()->find($criteria);

        if (!$user) {
            $this->renderJSON($ret);
            Yii::app()->end();
        }

        $user_roles = Yii::app()->user->getRole($user->id);

        $is_prescriber = in_array('Prescribe', array_values($user_roles));
        $is_med_admin = in_array('Med Administer', array_values($user_roles));
        if ($assignment->checkAuth($user) || $is_prescriber || $is_med_admin) {
            $ret['success'] = 1;
            $ret['payload'] = array(
                'id' => $user->id,
                'name' => $user->getFullName(),
            );
        }
        $audit_flag = $ret['success'] ? 'Success' : 'Failed';
        Audit::add('PSD Assignment', 'check pin', "{$audit_flag}: User (id: {$user->id}) attempted to unlock Assignment (id: {$assignment_id})");
        $this->renderJSON($ret);
    }

    public function actionUnlockPSD()
    {
        $data = \Yii::app()->request->getParam('Assignment', array());
        $patient_id = array_key_exists('patient_id', $data) ? $data['patient_id'] : null;
        $step_id = \Yii::app()->request->getParam('step_id', null);

        $this->actionGetPathStep(0, $step_id, $patient_id, 1);
    }

    public function actionConfirmAdministration()
    {
        $step_id = \Yii::app()->request->getParam('step_id');
        $step = PathwayStep::model()->findByPk($step_id);
        if (!$step) {
            throw new CHttpException(404, 'Unable to retrieve step for processing.');
        }
        $assignment_id = $step->getState('assignment_id');
        $patient_id = $step->pathway->worklist_patient->patient_id;
        $assignment_data = \Yii::app()->request->getParam('Assignment', array());
        \Yii::app()->event->dispatch(
            'step_progress',
            ['step' => $step, 'assignment' => $assignment_data, 'assignment_id' => $assignment_id, 'patient_id' => $patient_id]
        );
        $this->actionGetPathStep(0, $step_id, $patient_id, 0);
    }
}
