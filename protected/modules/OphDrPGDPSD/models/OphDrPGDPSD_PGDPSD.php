<?php
/**
 * (C) Copyright Apperta Foundation 2022
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

/**
 * This is the model class for table "ophdrpgdpsd_pgdpsd".
 *
 * The followings are the available columns in table 'ophdrpgdpsd_pgdpsd':
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $type
 * @property integer $active
 * @property integer $last_modified_user_id
 * @property string $last_modified_date
 * @property integer $created_user_id
 * @property string $created_date
 * The followings are the available model relations:
 * @property User $createdUser
 * @property User $lastModifiedUser
 * @property OphDrPGDPSD_PGDPSDMeds[] $medications
 * @property OphDrPGDPSD_AssignedUser[] $assigned_users
 * @property OphDrPGDPSD_AssignedTeam[] $assigned_teams
 */
class OphDrPGDPSD_PGDPSD extends \BaseActiveRecordVersioned
{
    public $temp_user_ids = array();
    public $temp_team_ids = array();
    public $temp_meds_info = array();
    public $temp_users = array();
    public $temp_teams = array();
    public $temp_meds = array();
    public $meds_info = '';
    protected $auto_update_relations = true;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'ophdrpgdpsd_pgdpsd';
    }
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, type, active', 'required'),
            array('temp_users, temp_teams', 'userAssignmentValidator'),
            array('temp_meds', 'medAssignmentValidator'),
            array('description', 'validateDescription'),
            array('name', 'length', 'max' => 42),
            array('institution_id', 'default', 'value' => Yii::app()->session->get('selected_institution_id'), 'on' => 'insert'),
            array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
            // The following rule is used by search().
            array('id, name, description, type', 'safe', 'on'=>'search'),
            array('name, type, active, description, assigned_meds, users, teams', 'safe'),
        );
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
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'assigned_meds' => array(self::HAS_MANY, 'OphDrPGDPSD_PGDPSDMeds', 'pgdpsd_id'),
            'users' => array(self::MANY_MANY, 'User', 'ophdrpgdpsd_assigneduser(pgdpsd_id, user_id)'),
            'teams' => array(self::MANY_MANY, 'Team', 'ophdrpgdpsd_assignedteam(pgdpsd_id, team_id)'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
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
            'type' => 'Type',
            'description' => 'Description',
            'active' => 'Active',
            'temp_users' => 'User List',
            'temp_teams' => 'Team List',
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
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('type', $this->type);
        $criteria->compare('description', $this->description);
        $criteria->compare('active', $this->active);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OphDrPGDPSD_PGDPSD the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function beforeValidate()
    {
        $this->cacheAssignedUsers();
        $this->cacheAssignedTeams();
        $this->cacheAssignedMeds();
        return parent::beforeValidate();
    }

    protected function cacheAssignedUsers()
    {
        $this->temp_users = array();
        foreach ($this->temp_user_ids as $user_id) {
            $temp_assign = array_filter($this->users, function ($user) use ($user_id) {
                return intval($user->id) === intval($user_id);
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
        $this->temp_teams = array();
        foreach ($this->temp_team_ids as $temp_team_id) {
            $temp_assign = array_filter($this->teams, function ($team) use ($temp_team_id) {
                return intval($team->id) === intval($temp_team_id);
            });
            if (!$temp_assign) {
                $temp_assign = Team::model()->findByPk($temp_team_id);
            } else {
                $temp_assign = array_values($temp_assign)[0];
            }
            $this->temp_teams[] = $temp_assign;
        }

        $this->teams = $this->temp_teams;
    }

    protected function cacheAssignedMeds()
    {
        $this->temp_meds = array();
        foreach ($this->temp_meds_info as $med) {
            $temp_assigned_med = new OphDrPGDPSD_PGDPSDMeds();
            $temp_assigned_med->attributes = $med;
            $this->temp_meds[] = $temp_assigned_med;
        }
        $this->assigned_meds = $this->temp_meds;
    }

    public function userAssignmentValidator($attribute_name, $param)
    {
        if ((!$this->users && !$this->temp_users) && (!$this->teams && !$this->temp_teams)) {
            if (!isset($this->getErrors()['Users'])) {
                $this->addError('Users', "Team or User list cannot be blank.");
            }
            return false;
        }
        return true;
    }
    public function validateDescription($attribute_name)
    {
        if (strtolower($this->type) === 'pgd' && !$this->$attribute_name) {
            $attr_label = $this->getAttributeLabel($attribute_name);
            $this->addError($attribute_name, "{$attr_label} cannot be blank to PGD");
            return false;
        }
        return true;
    }
    public function medAssignmentValidator($attribute_name)
    {
        $hasErrors = false;

        if (!$this->assigned_meds && !$this->$attribute_name) {
            $this->addError('Medications', 'Medication List cannot be blank');
            return false;
        }
        foreach ($this->temp_meds as $temp_med) {
            $temp_med->pgdpsd = $this;
            $temp_med->validate();
            $errors = $temp_med->getErrors();
            if ($errors) {
                $med_name = $temp_med->medication ? $temp_med->medication->getLabel(true) : '';

                foreach ($errors as $attr => $msg) {
                    if ($attr === 'pgdpsd_id') {
                        continue;
                    }
                    $this->addError('Medications', "{$med_name} {$msg[0]}");
                }
                $hasErrors = true;
            }
        }
        return !$hasErrors;
    }

    public function getAssignedMedsInJSON($prepend_markup = true)
    {
        $assigned_meds = array_map(function ($assigned_med) use ($prepend_markup) {
            $ret = array(
                'id' => $assigned_med->id,
                'medication_id' => $assigned_med->medication_id,
                'preferred_term' => $assigned_med->medication->getLabel(true),
                'dose' => $assigned_med->dose,
                'dose_unit_term' => $assigned_med->dose_unit_term,
                'route' => "$assigned_med->route",
                'route_id' => $assigned_med->route_id,
                'is_eye_route' => $assigned_med->route->isEyeRoute(),
                'allergy_ids' => array_map(function ($e) {
                    return $e->id;
                }, $assigned_med->medication->allergies),
            );
            if ($prepend_markup) {
                $info_box = new MedicationInfoBox();
                $info_box->medication_id = $assigned_med->medication_id;
                $info_box->init();
                $tooltip = $info_box->getHTML();
                $ret['prepended_markup'] = $tooltip;
            }
            return $ret;
        }, $this->assigned_meds);
        return json_encode($assigned_meds);
    }

    public function getAuthedUserIDs()
    {
        $authed_users = array();
        foreach ($this->teams as $team) {
            $authed_users = array_merge($authed_users, $team->getAllUsers());
        }
        $authed_users = array_merge($authed_users, $this->users);
        return array_map(function ($user) {
            return $user->id;
        }, $authed_users);
    }

    public function serialiseMedicationAssignments($laterality)
    {
        $meds = array();
        foreach ($this->assigned_meds as $key => $medication) {
            $entry = array();
            $entry['pair_key'] = $key;
            $entry['medication_id'] = $medication->medication_id;
            $entry['dose'] = $medication->dose;
            $entry['dose_unit_term'] = $medication->dose_unit_term;
            $entry['route_id'] = $medication->route_id;
            $entry['laterality'] = $medication->route->has_laterality ? $laterality : null;

            if (
                isset($entry['laterality'])
                && $entry['laterality']
                && (int)$entry['laterality'] === MedicationLaterality::BOTH
            ) {
                $entry['pair_key'] = $key + 1;
                $dup_entry = $entry;
                $entry['laterality'] = MedicationLaterality::RIGHT;
                $dup_entry['laterality'] = MedicationLaterality::LEFT;
                $meds[] = $dup_entry;
            }
            $meds[] = $entry;
        }
        return $meds;
    }
}
