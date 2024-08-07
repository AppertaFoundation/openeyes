<?php
class TeamController extends BaseAdminController
{
    public $layout = 'admin';

    public $group = 'Core';

    private $index_url = '/oeadmin/team/list';
    protected $api;

    protected function beforeAction($action)
    {
        $this->api = \Yii::app()->moduleAPI->get('OphDrPGDPSD');
        return parent::beforeAction($action);
    }

    private function userPopupOptions($team_id = null)
    {
        if (!$this->api) {
            $this->api = \Yii::app()->moduleAPI->get('OphDrPGDPSD');
        }

        $user_auth_objs = [];

        // Show users for all institutions if the user is admin and the team is being created ($team_id === null)
        // or if the user has a task that is the parent of OprnShowAllUsersInAdder for the team being edited ($team_id !== null)
        if (
            ($team_id && $this->checkAccess('OprnShowAllUsersInAdder', $team_id)) ||
            (!$team_id && $this->checkAccess('admin'))
        ) {
            $user_auth_objs = \UserAuthentication::model()->findAll('active = 1');
        } else {
            // Otherwise only allow users from the current institution
            $user_auth_objs = $this->api->getInstitutionUserAuth();
        }

        $ret = array();
        foreach ($user_auth_objs as $user_auth) {
            $user_id = $user_auth->user_id;
            $ret[$user_id] = $user_auth->user->getUserPermissionDetails();
        }

        return array_values($ret);
    }

    private function teamCriteria($has_with = true)
    {
        $criteria = new CDbCriteria();
        if ($has_with) {
            $criteria->addCondition('is_parentTeam.parent_team_id IS NULL');
        }
        $criteria->addCondition('t.active = 1');

        return $criteria;
    }

    private function teamPopupOptions($team)
    {
        $team_criteria = $this->teamCriteria();
        if (!$team->isNewRecord) {
            $team_criteria->addCondition('t.id != :current_team');
            $team_criteria->params[':current_team'] = $team->id;
        }
        $team_objs = Team::model()->with('is_parentTeam')->findAll($team_criteria);
        $teams = array_map(function ($team) {
            return array(
                'id' => $team->id,
                'label' => $team->name,
                'email' => $team->contact ? $team->contact->email : '',
            );
        }, $team_objs);
        return $teams;
    }

    private function gatherUserTaskAssigments($user_data)
    {
        return array_reduce(
            $user_data,
            static function ($assignments, $user) {
                $assignments[$user['id']] = $user['task'];

                return $assignments;
            },
            []
        );
    }

    private function createOrEditTeam(Team $team, $title_action)
    {
        $errors = [];

        if (Yii::app()->request->isPostRequest) {
            $team_data = Yii::app()->request->getParam('Team', []);
            $contact_data = Yii::app()->request->getParam('Contact', []);

            $errors = $this->saveTeam($team, $title_action, $team_data, $contact_data);

            if (empty($errors)) {
                $this->redirect($this->index_url);
            }
        }

        $user_options = $this->userPopupOptions($team->id);
        $team_options = $this->teamPopupOptions($team);

        $params = [
            'title_action' => $title_action,
            'errors' => $errors,
            'team' => $team,
            'teams' => $team_options,
            'users' => $user_options,
            'cancel_url' => $this->index_url,
            'prefix' => get_class($team),
            'super_user' => Yii::app()->user->checkAccess('Super Team Manager'),
        ];

        $this->render('/oeadmin/team/edit', $params);
    }

    /**
     * saveTeam
     *
     * @param $team Team The team model object to save
     * @param $title_action string The name of the action being taken, used when adding the audit entry
     * @param $team_data
     * @param $contact_data
     * @return array Any errors saving $team
     */
    private function saveTeam(Team $team, string $title_action, $team_data, $contact_data): array
    {
        $errors = [];

        $team_attributes = array_key_exists('attributes', $team_data) ? $team_data['attributes'] : array();
        $contact_attributes = array_key_exists('attributes', $contact_data) ? $contact_data['attributes'] : array();

        $users = array_key_exists('user', $team_data) ? $team_data['user'] : array();
        $child_teams = array_key_exists('team_assign', $team_data) ? $team_data['team_assign'] : array();

        if ($contact_attributes) {
            $team_attributes['email'] = $contact_attributes['email'];
        }

        $team->temp_user_ids = array_map(
            static function ($user) {
                return $user['id'];
            },
            $users
        );

        $team->temp_child_team_ids = $child_teams;
        $team->attributes = $team_attributes;

        $transaction = Yii::app()->db->beginTransaction();

        try {
            if ($team->save()) {
                $team->setUserTasks($this->gatherUserTaskAssigments($users));
            }

            $audit_user_data = implode(', ', $team->temp_user_ids);
            $audit_team_data = implode(', ', $team->temp_child_team_ids);

            $errors = $team->getErrors();

            if (!$errors) {
                Audit::add('admin-Team', strtolower($title_action), "Team id: {$team->id}, Assigned Users: {$audit_user_data}, Assigned Teams: {$audit_team_data}");

                $transaction->commit();
            } else {
                $transaction->rollback();
            }
        } catch (Exception $e) {
            $transaction->rollback();

            throw $e;
        }

        return $errors;
    }

    public function actionList()
    {
        $criteria = new CDbCriteria();

        $current_user = Yii::app()->user;

        $owned_or_managed_team_ids = [];

        if ($current_user->checkAccess('Super Team Manager')) {
            $owned_or_managed_team_ids = array_map(static function ($team) {
                return $team->id;
            }, Team::model()->findAll());
        } else {
            $owned_or_managed_team_ids = Team::getTeamIdsForUser($current_user->id, Team::ADMIN_VISIBLE_TASKS);
        }

        $criteria->addInCondition('t.id', $owned_or_managed_team_ids);

        if (!empty($_GET['search'])) {
            $criteria->join = 'LEFT JOIN contact c ON c.id = t.contact_id';
            $criteria->compare('LOWER(t.name)', strtolower($_GET['search']), true, 'OR');
            $criteria->compare('LOWER(c.email)', strtolower($_GET['search']), true, 'OR');
            $criteria->compare('t.id', $_GET['search'], false, 'OR');
        }

        $teams = Team::model()->findAll($criteria);

        $search_uri = $this->index_url;
        $delete_uri = '/oeadmin/team/delete';
        $pagination = $this->initPagination(Team::model(), $criteria);
        $search = !empty($_GET['search']) ? $_GET['search'] : '';
        Audit::add('admin-Team', 'list');
        $params = array(
            'teams' => $teams,
            'search_uri' => $search_uri,
            'delete_uri' => $delete_uri,
            'search' => $search,
            'pagination' => $pagination,
        );
        $this->render($this->index_url, $params);
    }

    public function actionAdd()
    {
        $team = new Team();
        $this->createOrEditTeam($team, 'Add');
    }

    public function actionEdit($id)
    {
        $team = Team::model()->findByPk($id);

        if (!$team) {
            Yii::app()->user->setFlash('team-not-found', 'Selected team does not exist');
            $this->redirect($this->index_url);
        }

        $this->createOrEditTeam($team, 'Edit');
    }

    public function actionDelete()
    {
        $result = 1;
        $team_data = Yii::app()->request->getParam('Team', array());
        if ($team_data) {
            $teams = Team::model()->findAllByPK($team_data);
            foreach ($teams as $team) {
                if (!$team->active) {
                    continue;
                }
                try {
                    $team->active = 0;
                    if (!$team->save(false)) {
                        $result = 0;
                    }
                } catch (Exception $e) {
                    $result = 0;
                }

                if ($result) {
                    Audit::add('admin-Team', 'deactivate');
                }
            }
        }
        echo $result;
    }

    // ajax call and list team members
    public function actionCheckTeamMembers($id)
    {
        $team = Team::model()->findByPk($id);
        $members = array();
        if ($team) {
            if (!$this->api) {
                $this->api = \Yii::app()->moduleAPI->get('OphDrPGDPSD');
            }
            $member_objs = $team->getAllUsers();
            $user_ids = array_map(function ($member) {
                return $member->id;
            }, $member_objs);
            $user_auth_objs = $this->api->getInstitutionUserAuth(true, $user_ids);
            foreach ($user_auth_objs as $user_auth) {
                $user_id = $user_auth->user_id;
                $members[$user_id] = $user_auth;
            }
            $members = array_map(function ($member) {
                return array(
                    'name' => $member->user->getFullNameAndTitle(),
                    'id' => $member->user->id,
                    'tooltips' => $member->user->getUserPermissionDetails(true),
                );
            }, $members);
        }
        $this->renderJSON($members);
    }

    // for team search in adder popup
    public function actionAutocomplete($team_id = null, $term = '')
    {
        $res = array();
        if (\Yii::app()->request->isAjaxRequest && !empty($term)) {
            $term = strtolower($term);
            $criteria = new \CDbCriteria();
            $criteria->compare("LOWER(t.name)", $term, true, 'OR');
            $criteria->compare('t.active', true);
            $criteria->with = ['is_parentTeam'];
            $criteria->addCondition('is_parentTeam.parent_team_id IS NULL');
            if ($team_id) {
                $criteria->addCondition('t.id != :team_id');
                $criteria->params[':team_id'] = $team_id;
            }
            foreach (\Team::model()->findAll($criteria) as $team) {
                $res[] = array(
                    'id' => $team->id,
                    'label' => $team->name,
                    'value' => $team->id,
                );
            }
        }
        $this->renderJSON($res);
    }
}
