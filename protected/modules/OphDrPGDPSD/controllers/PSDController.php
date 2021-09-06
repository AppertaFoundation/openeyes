<?php

class PSDController extends DefaultController
{
    protected static $action_types = array(
        'getSetMedications' => self::ACTION_TYPE_FORM,
        'getMedication' => self::ACTION_TYPE_FORM,
        'getPathStep' => self::ACTION_TYPE_FORM,
        'createPSD' => self::ACTION_TYPE_FORM,
        'RemovePSD' => self::ACTION_TYPE_FORM,
        'unlockPathStep' => self::ACTION_TYPE_FORM,
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
     * @param $partial
     * @param $pathstep_id
     * @param $patient_id
     * @param bool $interactive
     * @throws CHttpException
     * @throws Exception
     */
    public function actionGetPathStep($partial, $pathstep_id, $patient_id, $interactive = true)
    {
        $step = PathwayStep::model()->findByPk($pathstep_id);

        if (!$step) {
            throw new CHttpException(404, 'Unable to retrieve path step.');
        }

        $institution_auth = InstitutionAuthentication::model()->find('institution_id = :id', [':id' => Yii::app()->session['selected_institution_id']]);

        if (!$institution_auth) {
            throw new Exception('Unable to retrieve institution authentication information.');
        }

        $user_auth = UserAuthentication::model()->find(
            'user_id = :id AND institution_authentication_id = :institution_auth_id',
            [':id' => Yii::app()->user->id, ':institution_auth_id' => $institution_auth->id]
        );

        if (!$user_auth) {
            $for_administer = false;
        } else {
            $for_administer = $step->pincode === $user_auth->pincode;
        }


        $assignment = OphDrPGDPSD_Assignment::model()->find(
            'patient_id = :patient_id AND visit_id = :visit_id',
            [':patient_id' => $patient_id, ':visit_id' => $step->pathway->worklist_patient_id]
        );

        if (!$assignment) {
            throw new CHttpException(404, 'Unable to retrieve PSD.');
        }
        $can_remove_psd = Yii::app()->user->checkAccess('Prescribe')
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
        $this->renderJSON($dom);
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
        if (!$this->api) {
            $this->api = \Yii::app()->moduleAPI->get('OphDrPGDPSD');
        }
        $user_auth_objs = $this->api->getInstitutionUserAuth($pincode);
        if (!$user_auth_objs) {
            $this->renderJSON($ret);
            Yii::app()->end();
        }
        $users = array();
        foreach ($user_auth_objs as $user_auth) {
            $user_id = $user_auth->user_id;
            $users[$user_id] = $user_auth->user;
        }
        $users = array_values($users);

        if (count($users) !== 1) {
            $this->renderJSON($ret);
            Yii::app()->end();
        }
        $user = $users[0];
        if ($assignment && $user) {
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
        }
        $user = $ret['payload'] ? $ret['payload']['id'] : 'Not Found or Authorized';
        Audit::add('PSD Assignment', 'check pin', "Assignment id: {$assignment_id}, Accessed User: {$user}");
        $this->renderJSON($ret);
    }
}
