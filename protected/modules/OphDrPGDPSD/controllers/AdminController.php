<?php
class AdminController extends BaseAdminController
{
    public $group = 'PGD/PSD';
    public $delete_url = '/OphDrPGDPSD/admin/deletePGDPSDs';
    public $index_url = '/OphDrPGDPSD/admin/PGDPSDSettings';
    public $add_url = '/OphDrPGDPSD/admin/addPGDPSD';
    protected $api;

    protected function beforeAction($action)
    {
        $this->api = \Yii::app()->moduleAPI->get('OphDrPGDPSD');
        return parent::beforeAction($action);
    }
    private function adderPopupOptions()
    {
        $team_objs = \Team::model()->findAll('active = 1');
        $teams = array_map(function ($team) {
            return array(
                'id' => $team->id,
                'label' => $team->name,
                'email' => $team->contact ? $team->contact->email : '',
            );
        }, $team_objs);

        if (!$this->api) {
            $this->api = \Yii::app()->moduleAPI->get('OphDrPGDPSD');
        }
        $user_auth_objs = $this->api->getInstitutionUserAuth();
        $ret = array();
        foreach ($user_auth_objs as $user_auth) {
            $user_id = $user_auth->user_id;
            $ret[$user_id] = $user_auth->user->getUserPermissionDetails();
        }
        return array(
            'teams' => $teams,
            'users' => array_values($ret),
        );
    }

    public function actionPGDPSDSettings()
    {
        $criteria = new CDbCriteria();
        if (!empty($_GET['search'])) {
            $criteria->compare('LOWER(name)', strtolower($_GET['search']), true);
            $criteria->compare('LOWER(description)', strtolower($_GET['search']), true, 'OR');
        }
        $pgdpsds = OphDrPGDPSD_PGDPSD::model()->findAll($criteria);
        $pagination = $this->initPagination(OphDrPGDPSD_PGDPSD::model(), $criteria);
        $search = !empty($_GET['search']) ? $_GET['search'] : '';
        Audit::add('admin-PGDPSD-Settings', 'list');
        $params = array(
            'pgdpsds' => $pgdpsds,
            'search_url' => $this->index_url,
            'delete_url' => $this->delete_url,
            'add_url' => $this->add_url,
            'search' => $search,
            'pagination' => $pagination,
        );
        $this->render('pgdpsdsettings/list', $params);
    }

    public function actionAddPGDPSD()
    {
        $pgdpsd = new OphDrPGDPSD_PGDPSD();
        $this->savePGDPSD($pgdpsd, 'Add');
    }

    public function actionEditPGDPSD($id)
    {
        $pgdpsd = OphDrPGDPSD_PGDPSD::model()->findByPk($id);
        if (!$pgdpsd) {
            Yii::app()->user->setFlash('pgdpsd-not-found', 'Selected PGD/PSD does not exist');
            $this->redirect($this->index_url);
        }
        $this->savePGDPSD($pgdpsd, 'Edit');
    }

    private function savePGDPSD(OphDrPGDPSD_PGDPSD $pgdpsd, $action)
    {
        $pgdpsd_api = \Yii::app()->moduleAPI->get('OphDrPGDPSD');
        $errors = array();
        $prefix = get_class($pgdpsd);
        if (Yii::app()->request->isPostRequest) {
            $transaction = Yii::app()->db->beginTransaction();
            $data = Yii::app()->request->getParam($prefix, array());
            $pgdpsd->attributes = array_key_exists('attributes', $data) ? $data['attributes'] : array();
            $pgdpsd->temp_team_ids = array_key_exists('team_assign', $data) ? $data['team_assign'] : array();
            $pgdpsd->temp_user_ids = array_key_exists('user', $data) ? $data['user'] : array();
            $pgdpsd->temp_meds_info = array_key_exists('meds', $data) ? $data['meds'] : array();
            $pgdpsd->save();
            $audit_team_data = implode(', ', $pgdpsd->temp_team_ids);
            $audit_user_data = implode(', ', $pgdpsd->temp_user_ids);
            $audit_med_data = array_map(function ($med) {
                return $med['medication_id'];
            }, $pgdpsd->temp_meds_info);
            $audit_med_data = implode(',', $audit_med_data);
            $errors = $pgdpsd->getErrors();
            if (!$errors) {
                $transaction->commit();
                Audit::add('admin-PGDPSD-Settings', strtolower($action), "PGD/PSD id: {$pgdpsd->id}, Assigned Teams: {$audit_team_data}, Assigned Users: {$audit_user_data}, Assigned Meds: {$audit_med_data}");
                $this->redirect($this->index_url);
            } else {
                $transaction->rollback();
            }
        }

        $adder_popup_options = $this->adderPopupOptions();
        $medications = $pgdpsd_api->getMedicationOptions();
        $params = array(
            'title_action' => $action,
            'errors' => $errors,
            'pgdpsd' => $pgdpsd,
            'teams' => $adder_popup_options['teams'],
            'users' => $adder_popup_options['users'],
            'medications' => $medications,
            'cancel_url' => $this->index_url,
            'prefix' => $prefix,
        );
        $this->render('pgdpsdsettings/edit', $params);
    }

    public function actionDeletePGDPSDs()
    {
        $result = 1;
        $pgdpsd_data = Yii::app()->request->getParam('PGDPSDs', array());
        $deactivated = array();
        if ($pgdpsd_data) {
            $pgdpsds = OphDrPGDPSD_PGDPSD::model()->findAllByPK($pgdpsd_data);
            foreach ($pgdpsds as $pgdpsd) {
                if (!$pgdpsd->active) {
                    continue;
                }
                try {
                    $pgdpsd->active = 0;
                    if (!$pgdpsd->save(false)) {
                        $result = 0;
                    } else {
                        $deactivated[] = $pgdpsd->id;
                    }
                } catch (Exception $e) {
                    $result = 0;
                }
            }
            $deactivated_ids = implode(', ', $deactivated);
            if ($result) {
                Audit::add('admin-PGDPSD-Settings', 'deactivate', "Deactivated pgdpsd id(s): {$deactivated_ids}");
            }
        }
        echo $result;
    }
}
