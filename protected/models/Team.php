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
 * @property Contact[] $contact
 */
class Team extends BaseActiveRecordVersioned
{
    use HasFactory;

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
            'condition' =>"$table_alias.institution_id = :institution_id",
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
            array('active', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>255),
            array('institution_id', 'default', 'value' => Yii::app()->session->get('selected_institution_id'), 'on' => 'insert'),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            array('last_modified_date, created_date, users, childTeams, active, name', 'safe'),
            // The following rule is used by search().
            array('id, name, contact_id, active', 'safe', 'on'=>'search'),
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
        $criteria=new CDbCriteria;
        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('contact_id', $this->contact_id, true);
        $criteria->compare('active', $this->active);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
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
                if ($temp_assign->is_parentTeam) {
                    $this->addError('Nested Team', "{$temp_assign->name} is a parent team");
                }
            } else {
                $temp_assign = array_values($temp_assign)[0];
            }
            $this->temp_child_teams[] = $temp_assign;
        }

        $this->childTeams = $this->temp_child_teams;
    }

    public function beforeValidate()
    {
        $this->cacheAssignedUsers();
        $this->cacheAssignedTeams();
        return parent::beforeValidate();
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
            $errors = $contact->getErrors();
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

    public function getParentTeamLinks()
    {
        $names = array_map(function ($parent_team) {
            $color_class = $parent_team->active ? 'good' : 'warning';
            return "<span class='highlighter $color_class'><a target='_blank' href='/oeadmin/team/edit/$parent_team->id'>$parent_team->name</a></span>";
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
}
