<?php
/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OE\factories\models\traits\HasFactory;
use OEModule\OphCoMessaging\models\Mailbox;

/**
 * This is the model class for table "team".
 *
 * The followings are the available columns in table 'team':
 * @property integer $id
 * @property string $name
 * @property string $contact_id
 * @property integer $active
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 *
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property TeamTeamAssign[] $teamTeamAssigns_child
 * @property TeamTeamAssign[] $teamTeamAssigns_parent
 * @property TeamUserAssign[] $teamUserAssigns
 * @property Contact $contact
 * @property Team[] $childTeams
 * @property Team[] $parentTeams
 * @property Mailbox[] $mailboxes
 */
class Team extends BaseActiveRecordVersioned
{
    use HasFactory;
    use \APICache;

    public const TASK_OWNER = 'TaskOwnTeam';
    public const TASK_MANAGER = 'TaskManageTeam';
    public const TASK_MEMBER = 'TaskMemberOfTeam';

    public const DEFAULT_TASK = self::TASK_MEMBER;
    public const ALL_TASKS = [self::TASK_MEMBER, self::TASK_MANAGER, self::TASK_OWNER];
    public const ADMIN_VISIBLE_TASKS = [self::TASK_OWNER, self::TASK_MANAGER];

    public const TEAM_ASSIGNMENT_BIZ_RULE = 'hasTeamAssignment';

    public $email = '';
    public $temp_user_ids = array();
    public $temp_users = array();
    public $temp_child_team_ids = array();
    public $temp_child_teams = array();
    protected $auto_update_relations = true;
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Team the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * getTasksList
     *
     * This function is used for retrieving the display names for the Team tasks.
     * These display names can then be used for views inside tables, select boxes etc
     * to show the user facing name of the task.
     *
     * These names are stored in the description field of the AuthItem associated
     * with the task.
     *
     * An associative array of the structure ['task name' => 'display name', ...] will
     * be returned where the task names are the constants defined above starting with TASK_...
     * e.g. [Team::TASK_MANAGER => 'Manager']
     *
     * @param $tasks array The set of Team AuthItem tasks to retrieve the descriptions from
     * @return array An associative array of Tasks => Task descriptions
     */
    public static function getTasksList($tasks)
    {
        $auth_items = self::filterAuthItemsByTasks(
            self::filterTasks($tasks),
            Yii::app()->authManager->getTasks(),
        );

        // Per the PHP documentation for array_map, the keys (which are task names) are preserved
        // when only one array is supplied, so the mappings are not destroyed.
        return array_map(
            static function ($auth_item) {
                return $auth_item->description;
            },
            $auth_items
        );
    }

    /**
     * getTeamIdsForUserGroupedByTask
     *
     * Retrieves the team ids for the provided user and team tasks, grouping the ids
     * by the task they belong to.
     *
     * e.g. [TASK_OWNER => [team id, ...], TASK_MANAGER => [team id, ...], ...]
     *
     * @param $user_id mixed The id of the user to get associated teams for
     * @param $tasks array The set of tasks to retrieve and group the team ids for
     * @return array
     */
    public static function getTeamIdsForUserGroupedByTask($user_id, $tasks): array
    {
        $auth_assignments = self::filterAuthItemsByTasks(
            self::filterTasks($tasks),
            Yii::app()->authManager->getAuthAssignments($user_id)
        );

        return array_map(
            static function ($auth_item) {
                return $auth_item->getData();
            },
            $auth_assignments
        );
    }

    /**
     * getTeamIdsForUser
     *
     * Retrieves the team ids for the provided user and team tasks without grouping by task.
     *
     * @param $user_id mixed The id of the user to get associated teams for
     * @param $tasks array The set of tasks to retrieve the team ids for
     * @return array
     */
    public static function getTeamIdsForUser($user_id, $tasks): array
    {
        return call_user_func_array(
            'array_merge',
            array_values(self::getTeamIdsForUserGroupedByTask($user_id, $tasks))
        );
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'team';
    }

    public function defaultScope()
    {
        $selected_institution_id = Yii::app()->session->get('selected_institution_id');
        if (!$selected_institution_id) {
            return array();
        }
        $table_alias = $this->getTableAlias(false, false);
        return array(
            'condition' => "$table_alias.institution_id = :institution_id",
            'params' => array(
                ':institution_id' => $selected_institution_id,
            )
        );
    }
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, active', 'required'),
            array('email', 'email'),
            array('active', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 255),
            array('institution_id', 'default', 'value' => Yii::app()->session->get('selected_institution_id'), 'on' => 'insert'),
            array('last_modified_user_id, created_user_id', 'length', 'max' => 10),
            array('last_modified_date, created_date, users, childTeams, active, name', 'safe'),
            // The following rule is used by search().
            array('id, name, contact_id, active', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'teamUsers' => array(self::HAS_MANY, 'TeamUserAssign', 'team_id'),
            'users' => array(self::MANY_MANY, 'User', 'team_user_assign(team_id, user_id)'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
            'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
            'is_childTeam' => array(self::HAS_MANY, 'TeamTeamAssign', 'child_team_id'),
            'is_parentTeam' => array(self::HAS_MANY, 'TeamTeamAssign', 'parent_team_id'),
            'childTeams' => array(self::MANY_MANY, 'Team', 'team_team_assign(parent_team_id, child_team_id)'),
            'parentTeams' => array(self::MANY_MANY, 'Team', 'team_team_assign(child_team_id, parent_team_id)'),
            'mailboxes' => array(self::MANY_MANY, Mailbox::class, 'mailbox_team(team_id, mailbox_id)')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'active' => 'Active',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('contact_id', $this->contact_id, true);
        $criteria->compare('active', $this->active);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }


    /**
     * getUserTaskMappings
     *
     * Returns an associative array of user ids mapped to the task the user is assigned
     * in this team.
     *
     * @return The list of users mapped to their assigned tasks in this team
     */
    public function getUserTaskMappings(): array
    {
        return array_reduce(
            $this->getAuthAssignmentRows(self::ALL_TASKS, null),
            function ($assignments, $auth_item) {
                $team_ids = unserialize($auth_item['data']);

                if (in_array($this->id, $team_ids)) {
                    $assignments[$auth_item['userid']] = $auth_item['itemname'];
                }

                return $assignments;
            },
            []
        );
    }

    /**
     * getUsersWithAssignedTasks
     *
     * getUsersWithAssignedTasks presents the cached results of a call to the
     * getUserTaskMappings function, which itself returns an associative array
     * of user ids mapped to the task the user is assigned in this team.
     *
     * @return array The cached list of users mapped to their assigned tasks in this team
     */
    public function getUsersWithAssignedTasks(): array
    {
        return $this->getCachedData($this->id, [$this, 'getUserTaskMappings']);
    }

    /**
     * setUserTasks
     *
     * Takes an associative array of user id => task mappings and uses the application
     * AuthManager to set the data for the AuthItems entries for each user and for each task, by
     * adding and removing team ids from the arrays which constitute said data.
     *
     * @param $new_mappings array An array of ['user_id' => 'task'] mappings to set
     */
    public function setUserTasks($new_mappings)
    {
        $existing_mappings = $this->getUsersWithAssignedTasks();

        $to_preserve = array_intersect_assoc($existing_mappings, $new_mappings);
        $to_add = array_diff_assoc($new_mappings, $to_preserve);
        $to_remove = array_diff_assoc($existing_mappings, $to_preserve);

        $user_ids = array_merge(array_keys($to_add), array_keys($to_remove));

        $assignment_data = $this->getUserTaskTeamEntries(self::ALL_TASKS, $user_ids);
        $new_assignments = [];

        foreach ($to_add as $user_id => $task) {
            if (!in_array($task, self::ALL_TASKS)) {
                throw new Exception('The task ' . $task . ' provided to setUserTasks is not a Team task');
            }

            $new_assignments[$user_id][$task] = array_merge($assignment_data[$user_id][$task] ?? [], [$this->id]);
        }

        foreach ($to_remove as $user_id => $task) {
            $new_assignments[$user_id][$task] = array_diff($assignment_data[$user_id][$task], [$this->id]);
        }

        foreach ($new_assignments as $user_id => $tasks) {
            foreach ($tasks as $task => $team_ids) {
                if (count($team_ids) > 0) {
                    Yii::app()->authManager->setOrUpdateAssignment($task, $user_id, self::TEAM_ASSIGNMENT_BIZ_RULE, $team_ids);
                } else {
                    Yii::app()->authManager->revoke($task, $user_id);
                }
            }
        }

        $this->resetCacheData($this->id);
    }

    public function setAndCacheAssignedUsers($user_ids)
    {
        $this->temp_user_ids = $user_ids;

        $this->cacheAssignedUsers();
    }

    public function setAndCacheAssignedTeams($team_ids) {
        $this->temp_child_team_ids = $team_ids;

        $this->cacheAssignedTeams();
    }

    public function beforeValidate()
    {
        $this->cacheAssignedUsers();
        $this->cacheAssignedTeams();
        return parent::beforeValidate();
    }

    public function getParentTeamLinks()
    {
        $names = array_map(function ($parent_team) {
            $color_class = $parent_team->active ? 'good' : 'warning';
            return "<span class='highlighter $color_class'>"
            . "<a target='_blank' href='/oeadmin/team/edit/$parent_team->id'>$parent_team->name</a></span>";
        }, $this->parentTeams);
        return implode(', ', $names);
    }

    public function getAllUsers()
    {
        $authed_users = array();
        foreach ($this->users as $user) {
            $authed_users[$user->id] = $user;
        }
        if ($this->childTeams) {
            foreach ($this->childTeams as $child_team) {
                foreach ($child_team->getAllUsers() as $user) {
                    $authed_users[$user->id] = $user;
                }
            }
        }
        return $authed_users;
    }

    protected function cacheAssignedUsers()
    {
        $this->temp_users = array();
        foreach ($this->temp_user_ids as $user_id) {
            $temp_assign = array_filter($this->users, function ($team_user) use ($user_id) {
                return intval($team_user->id) === intval($user_id);
            });
            if (!$temp_assign) {
                $temp_assign = \User::model()->findByPk($user_id);
            } else {
                $temp_assign = array_values($temp_assign)[0];
            }
            $this->temp_users[] = $temp_assign;
        }
        $this->users = $this->temp_users;
    }

    protected function cacheAssignedTeams()
    {
        $this->temp_child_teams = array();
        foreach ($this->temp_child_team_ids as $child_team_id) {
            $temp_assign = array_filter($this->childTeams, function ($child_team) use ($child_team_id) {
                return intval($child_team->id) === intval($child_team_id);
            });
            if (!$temp_assign) {
                $temp_assign = self::model()->findByPk($child_team_id);
            } else {
                $temp_assign = array_values($temp_assign)[0];
            }

            if ($temp_assign->is_parentTeam) {
                $this->addError('Nested Team', "{$temp_assign->name} is a parent team");
            }

            $this->temp_child_teams[] = $temp_assign;
        }

        $this->childTeams = $this->temp_child_teams;
    }

    protected function beforeSave()
    {
        // if there is no users, the team will be inactive state
        if (!$this->temp_users && !$this->temp_child_teams) {
            $this->active = 0;
        }
        if ($this->email) {
            $contact = $this->contact;
            if (!$contact) {
                $contact = new Contact('team_contact');
                $contact_label_id = ContactLabel::model()->findByAttributes(array('name' => 'Team'))->id;
                $contact->contact_label_id = $contact_label_id;
            }
            $contact->email = $this->email;
            if ($contact->isModelDirty()) {
                $contact->save();
                $this->contact_id = $contact->id;
            }
            foreach ($contact->getErrors() as $attr => $error) {
                $this->addError($contact->getAttributeLabel($attr), $error);
            }
        }
        return parent::beforeSave();
    }

    protected function afterFind()
    {
        $this->email = $this->contact ? $this->contact->email : '';
        parent::afterFind();
    }

    /**
     * filterTasks
     *
     * Filter AuthManager tasks to those specific to the Team model
     *
     * @param $tasks array The list of AuthManager tasks
     * @return array
     */
    private static function filterTasks($tasks): array
    {
        return array_intersect($tasks, self::ALL_TASKS);
    }

    /**
     * filterAuthItemsByTasks
     *
     * Takes a set of tasks and an associated array of tasks => AuthItems
     * and returns only the AuthItems associated with a task in the set.
     *
     * @param $tasks array The tasks to filter the auth items by
     * @param $auth_items array An array of AuthItems indexed by tasks
     * @return array Filtered AuthItems
     */
    private static function filterAuthItemsByTasks($tasks, $auth_items): array
    {
        // $tasks is an array with tasks as the values and
        // $auth_items is an associated array with tasks as the keys,
        // so flip the values of $tasks into keys to allow array_intersect_key
        // to filter $auth_items.
        return array_intersect_key($auth_items, array_flip($tasks));
    }

    /**
     * getAuthAssignmentRows
     *
     * @param $tasks array The set of tasks to constrain the results to
     * @param $user_ids array|null Optional set of user ids to constrain the results to
     * @return array
     */
    private function getAuthAssignmentRows($tasks, $user_ids = null)
    {
        $command = Yii::app()->db->createCommand()
                                 ->select('itemname, userid, data')
                                 ->from('authassignment');

        $tasks = self::filterTasks($tasks);

        if (empty($user_ids)) {
            $command->join('team_user_assign', 'user_id = userid')
                    ->where(
                        ['and', ['in', 'itemname', $tasks], 'team_id = :team_id'],
                        [':team_id' => $this->id]
                    );
        } else {
            $command->where(
                ['and',
                 ['in', 'itemname', self::filterTasks($tasks)],
                 ['in', 'userid', $user_ids]
                ]
            );
        }

        return $command->queryAll();
    }

    /**
     * getUserTaskTeamEntries
     *
     * Assembles the data from the user AuthItems into a two level deep associative array.
     * It has the following structure: [user_id => [task => team_ids, ...], ...]
     *
     * @param $tasks array The set of tasks to constrain the results to
     * @param $user_ids array|null Optional set of user ids to constrain the results to
     * @return array The [user => [task => team ids]] mappings
     */
    private function getUserTaskTeamEntries($tasks, $user_ids = null): array
    {
        return array_reduce(
            $this->getAuthAssignmentRows($tasks, $user_ids),
            static function ($mappings, $row) {
                $mappings[$row['userid']][$row['itemname']] = unserialize($row['data']);

                return $mappings;
            },
            []
        );
    }
}
