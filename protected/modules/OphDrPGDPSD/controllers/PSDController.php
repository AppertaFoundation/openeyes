<?php

use OEModule\OphDrPGDPSD\models\OphDrPGDPSD_Assignment;

/**
 * @extends DefaultController
 */
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

    public function actionRemovePSD()
    {
        $ret = array(
            'success' => 0
        );
        $assignment_id = \Yii::app()->request->getParam('assignment_id', null);
        $assignment = OphDrPGDPSD_Assignment::model()->findByPk($assignment_id);
        if ($assignment && intval($assignment->status) === $assignment::STATUS_TODO) {
            $transaction = \Yii::app()->db->beginTransaction();
            $assignment->active = 0;
            $assignment->save();
        }
        if ($assignment->getErrors()) {
            $transaction->rollback();
        } else {
            $transaction->commit();
            $ret['success'] = 1;
            Audit::add('PSD Assignment', 'removed assignment', "Assignment id: {$assignment_id}");
        }
        $this->renderJSON($ret);
    }

    /**
     * @param $partial int              to display overview of the popup or full (with actions, detailed info)
     * @param $pathstep_id int         for PathwayStep
     * @param $visit_id int       for OphDrPGDPSD_Assignment
     * @param $pathstep_type_id int           for Patient
     * @param $for_administer int      to display form or read only content
     * @param int $interactive     to display popup buttons
     * @throws CHttpException
     * @throws Exception
     */
    public function actionGetPathStep($partial, $pathstep_id, $visit_id, $pathstep_type_id, $for_administer = false, $interactive = 1)
    {
        $step = PathwayStep::model()->findByPk($pathstep_id);
        $wl_patient = WorklistPatient::model()->findByPk($visit_id);

        if (!$step) {
            $step = PathwayTypeStep::model()->findByPk($pathstep_type_id);
            if (!$step) {
                throw new CHttpException(404, 'Unable to retrieve path step.');
            }
        }

        $assignment_id = $step->getState('assignment_id');

        if (!$assignment_id) {
            throw new CHttpException(404, 'Unable to retrieve PSD id.');
        }
        $assignment = OphDrPGDPSD_Assignment::model()->find('id = :id AND active = 1', [':id' => $assignment_id]);

        if (!$assignment) {
            throw new CHttpException(404, 'Unable to retrieve PSD.');
        }

        $pathway = $wl_patient->pathway;

        if (!$pathway) {
            throw new CHttpException(404, 'Unable to retrieve Pathway.');
        }

        $pathway->updateStatus();

        $can_remove_psd = \Yii::app()->user->checkAccess('Prescribe')
            && ($step instanceof PathwayTypeStep || (int)$step->status === PathwayStep::STEP_REQUESTED)
            && !$assignment->elements ? '' : 'disabled';
        $dom = $this->renderPartial(
            '/pathstep/pathstep_view',
            array(
                'assignment' => $assignment,
                'step' => $step,
                'partial' => (int)$partial,
                'visit' => $wl_patient,
                'patient_id' => $wl_patient->patient_id,
                'pathway' => $wl_patient->pathway,
                'for_administer' => $for_administer,
                'is_prescriber' => Yii::app()->user->checkAccess('Prescribe'),
                'can_remove_psd' => $can_remove_psd,
                'interactive' => (int)$interactive,
                'allow_unlock' => $assignment->getAppointmentDetails()['date'] === 'Today'
            ),
            true
        );
        $ret = array(
            'dom' => $dom,
            'step' => $step->toJSON(),
            'pathway_status' => $pathway->getStatusString(),
            'status_html' => $pathway->getPathwayStatusHTML(),
            'step_html' => $this->renderPartial('//worklist/_clinical_pathway', ['visit' => $wl_patient], true),
            'waiting_time_html' => $pathway->getTotalDurationHTML(true),
            'wait_time_details' => $pathway->getWaitTimeSinceLastAction(),
        );
        $this->renderJSON($ret);
    }

    public function actionCheckPincode()
    {
        $pincode = \Yii::app()->request->getParam('pincode', null);

        $assignment_id = Yii::app()->request->getParam('assignment_id', null);
        $assignment = OphDrPGDPSD_Assignment::model()->findByPk($assignment_id);

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
        $step_id = \Yii::app()->request->getParam('step_id', null);
        $step_type_id = \Yii::app()->request->getParam('step_type_id', null);
        $visit_id = \Yii::app()->request->getParam('visit_id', null);

        $wl_patient = WorklistPatient::model()->findByPk($visit_id);

        if (!$wl_patient->pathway->start_time) {
            $wl_patient->pathway->start_time = date('Y-m-d H:i:s');
            $wl_patient->pathway->save();
        }

        $this->actionGetPathStep(0, $step_id, $visit_id, $step_type_id, 1);
    }

    /**
     * @return void
     * @throws CHttpException
     * @throws JsonException
     */
    public function actionConfirmAdministration()
    {
        $step_id = \Yii::app()->request->getParam('step_id');
        $step_type_id = \Yii::app()->request->getParam('step_type_id');
        $visit_id = \Yii::app()->request->getParam('visit_id');
        $step = PathwayStep::model()->findByPk($step_id);
        if (!$step) {
            $type_step = PathwayTypeStep::model()->findByPk($step_type_id);

            if (!$type_step) {
                throw new CHttpException(404, 'Unable to retrieve step for processing.');
            }
            $visit = WorklistPatient::model()->findByPk($visit_id);
            $steps = $type_step->pathway_type->instancePathway($visit);
            $step = $steps[$step_type_id];
        }
        $assignment_id = $step->getState('assignment_id');
        $patient_id = $step->pathway->worklist_patient->patient_id;
        $assignment_data = \Yii::app()->request->getParam('Assignment', array());
        \Yii::app()->event->dispatch(
            'step_progress',
            ['step' => $step, 'assignment' => $assignment_data, 'assignment_id' => $assignment_id, 'patient_id' => $patient_id]
        );
        $this->actionGetPathStep(0, $step_id, $visit_id, $step_type_id, 0);
    }
}
