<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "User".
 *
 * The followings are the available columns in table 'User':
 *
 * @property int    $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property int    $active
 * @property string $password
 * @property string $salt
 * @property int    $global_firm_rights
 * @property date   $password_last_changed_date
 * @property int    $password_failed_tries
 * @property string $password_status
 * @property date   $password_softlocked_until
 */
class User extends BaseActiveRecordVersioned
{
    /**
     * Used to check password and password confirmation match.
     *
     * @var string
     */
    public $password_repeat;
    public $password_hashed;

    /**
     * Returns the static model of the specified AR class.
     *
     * @return User the static model class
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
        return 'user';
    }

    public function behaviors()
    {
        return array(
            'ContactBehavior' => array(
                'class' => 'application.behaviors.ContactBehavior',
            ),
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $commonRules = array(
            // Added for uniqueness of username
            array('username', 'unique', 'className' => 'User', 'attributeName' => 'username'),
            array('id, username, first_name, last_name, email, active, global_firm_rights', 'safe', 'on' => 'search'),
            array('title, first_name, last_name', 'match', 'pattern' => '/^[a-zA-Z]+(([\',. -][a-zA-Z ])?[a-zA-Z]*)*$/', 'message' => 'Invalid {attribute} entered.'),
            array(
                'username, first_name, last_name, email, active, global_firm_rights, title, qualifications, role, salt, password, is_consultant, is_surgeon,
                 has_selected_firms,doctor_grade_id, registration_code, signature_file_id',
                'safe',
            ),
        );
        $user = Yii::app()->request->getPost('User');
        // if the global firm rights is set to No, at least one context needs to be selected
        if (isset($user['global_firm_rights']) && intval($user['global_firm_rights']) === 0) {
            $commonRules = array_merge(
                $commonRules,
                array(
                    array('firms', 'required'),
                )
            );
        }

        if (Yii::app()->params['auth_source'] === 'BASIC') {
            $user = Yii::app()->request->getPost('User');

            // if the global firm rights is set to No, at least one context needs to be selected
            if (isset($user['global_firm_rights']) && $user['global_firm_rights'] == 0) {
                $commonRules = array_merge(
                    $commonRules,
                    array(
                        array('firms', 'required'),
                    )
                );
            }

            $pw_restrictions = $this->getPasswordRestrictions();
            $generalUserRules = array(
                array(
                    'username',
                    'match',
                    'pattern' => '/^[\w|\.\-_\+@]+$/',
                    'message' => 'Only letters, numbers and underscores are allowed for usernames.',
                ),
                array('username, email, first_name, last_name, active, global_firm_rights', 'required'),
                array('username, first_name, last_name', 'length', 'max' => 40),
                array(
                    'password',
                    'length',
                    'min' => $pw_restrictions['min_length'],
                    'tooShort' => $pw_restrictions['min_length_message'],
                    'max' => $pw_restrictions['max_length'],
                    'tooLong' => $pw_restrictions['max_length_message'],
                ),
                array('password','match','pattern'=> $pw_restrictions['strength_regex'],'message'=> $pw_restrictions['strength_message']),
                array('email', 'length', 'max' => 80),
                array('email', 'email'),
                array('salt', 'length', 'max' => 10),
                // Added for password comparison functionality
                array('password_repeat, password_last_changed_date, password_failed_tries, password_status, password_softlocked_until', 'safe'),
            );
            $surgeonRules = array(array('doctor_grade_id,registration_code ','required'));

            if (isset($user['is_surgeon']) && $user['is_surgeon'] == 1) {
                return array_merge($commonRules, $surgeonRules, $generalUserRules);
            } else {
                return array_merge($commonRules, $generalUserRules);
            }
        } elseif (Yii::app()->params['auth_source'] === 'LDAP') {
            return array_merge(
                $commonRules,
                array(
                    array('username, active, global_firm_rights', 'required'),
                    array('username', 'length', 'max' => 40),
                    array('password_repeat', 'safe'),
                )
            );
        } elseif (Yii::app()->params['auth_source'] === 'SAML' || Yii::app()->params['auth_source'] === 'OIDC') {
            return array_merge(
                $commonRules,
                array(
                    array('username, first_name, last_name, email, active', 'required'),
                    array('username', 'length', 'max' => 40),
                    array('email', 'length', 'max' => 40),
                    array('password_repeat', 'safe'),
                )
            );
        } else {
            throw new SystemException('Unknown auth_source: ' . Yii::app()->params['auth_source']);
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        $relations = array(
            'firmUserAssignments' => array(self::HAS_MANY, 'FirmUserAssignment', 'user_id'),
            'firms' => array(
                self::MANY_MANY,
                'Firm',
                'firm_user_assignment(firm_id, user_id)',
                'condition' => 'firms.active = 1',
            ),
            'firmRights' => array(self::MANY_MANY, 'Firm', 'user_firm_rights(firm_id, user_id)'),
            'serviceRights' => array(self::MANY_MANY, 'Service', 'user_service_rights(service_id, user_id)'),
            'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
            'firm_preferences' => array(self::HAS_MANY, 'UserFirmPreference', 'user_id'),
            'firmSelections' => array(
                self::MANY_MANY,
                'Firm',
                'user_firm(firm_id, user_id)',
                'condition' => 'firmSelections.active = 1',
                'order' => 'name asc',
            ),
            'siteSelections' => array(self::MANY_MANY, 'Site', 'user_site(site_id, user_id)', 'order' => 'name asc'),
            'grade' => array(self::BELONGS_TO, 'DoctorGrade', 'doctor_grade_id'),
            'signature' => array(self::BELONGS_TO, 'ProtectedFile', 'signature_file_id'),
        );

        if ($this->getScenario() !== 'portal_command') {
            $relations['preferred_firms'] = [
                self::HAS_MANY,
                'Firm',
                'firm_id',
                'through' => 'firm_preferences',
                'order' => 'firm_preferences.position DESC',
                'limit' => (string)SettingMetadata::model()->getSetting('recent_context_firm_limit'), //Method to get recent_context_firm_limit from setting_installation (default is 6)
                'group' => 'user_id, firm_id',
            ];
        }

        return $relations;
    }


    /**
     * @return mixed|null
     * @deprecated - since v2.2
     */
    public function getIs_doctor()
    {
        return $this->is_surgeon;
    }

    public function changeFirm($firm_id)
    {
        $this->last_firm_id = $firm_id;
        $criteria = new CDbCriteria();
        $criteria->addCondition('user_id = :user_id');
        $criteria->order = 'position DESC';
        $criteria->params = array(':user_id' => $this->id);
        $top_preference = UserFirmPreference::model()->find($criteria);
        $preference = UserFirmPreference::model()->find(
            'user_id = :user_id AND firm_id = :firm_id',
            array(':user_id' => $this->id, ':firm_id' => $firm_id)
        );
        if (!$preference) {
            $preference = new UserFirmPreference();
            $preference->user_id = $this->id;
            $preference->firm_id = $firm_id;
        }
        if (!$top_preference) {
            $preference->position = 1;
        } elseif ($top_preference->id != $preference->id) {
            $preference->position = $top_preference->position + 1;
        }
        if (!$preference->save()) {
            throw new CException('Error saving user firm preference');
        }
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'username' => 'Username',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email' => 'Email',
            'active' => 'Active',
            'password' => 'Password',
            'password_old' => 'Current password',
            'password_new' => 'New password',
            'password_confirm' => 'Confirm password',
            'global_firm_rights' => 'Global firm rights',
            'firms' => 'Context',
            'is_consultant' => 'Consultant',
            'is_surgeon' => 'Surgeon',
            'doctor_grade_id' => 'Grade',
            'role' => 'Position',
            'password_last_changed_date' => 'Date Password was last changed',
            'password_failed_tries' => 'Number of failed Password attempts',
            'password_status' => 'Status of User Password',
            'password_softlocked_until' => 'Password locked until',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('username', $this->username, true);
        $criteria->compare('first_name', $this->first_name, true);
        $criteria->compare('last_name', $this->last_name, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('active', $this->active);
        $criteria->compare('global_firm_rights', $this->global_firm_rights);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Hashes the user password for insertion into the db.
     */
    protected function afterValidate()
    {
        parent::afterValidate();

        if (!$this->password_hashed) {
            $this->salt = null;
            $this->password = $this->hashPassword($this->password, null);
            $this->password_hashed = true;
        }
    }

    /**
     * Returns an md5 hash of the password and username provided.
     *
     * @param string $password
     * @param string $salt
     *
     * @return string
     */
    public function hashPassword($password, $salt)
    {
        if (!$salt) {
            return password_hash($password, PASSWORD_BCRYPT);
        }
        return md5($salt . $password);
    }

    /**
     * Returns whether the password provided is valid for this user.
     *
     * Hashes the password with the salt for this user. If valid, return true,
     * else return false.
     *
     * @param string $password
     * @throws Exception
     *
     * @return bool
     */
    public function validatePassword($password)
    {
        if (!$this->salt) {
            return password_verify($password, $this->password);
        }
        if ($this->hashPassword($password, $this->salt) === $this->password) {
            // Regenerate the hash using the new method.
            $this->salt = null;
            $this->password = $this->hashPassword($password, null);
            if (!$this->saveAttributes(array('password','salt'))) {
                $this->audit('login', 'auto-encrypt-password-failed', "user_id = {$this->id}, with error :". var_export($this->getErrors(), true));
                return false;
            }
            $this->audit('login', 'auto-encrypt-password', "user_id = {$this->id}");
            return password_verify($password, $this->password);
        }
        return false;
    }

    /**
     * Displays a string indicating whether the user account is active.
     *
     * @return string
     */
    public function getActiveText()
    {
        if ($this->active) {
            return 'Yes';
        } else {
            return 'No';
        }
    }

    /**
     * Displays a string indicating whether the user account has global firm rights.
     *
     * @return string
     */
    public function getGlobalFirmRightsText()
    {
        if ($this->global_firm_rights) {
            return 'Yes';
        } else {
            return 'No';
        }
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return implode(' ', array($this->first_name, $this->last_name));
    }

    /**
     * @return string
     */
    public function getFullNameAndUserName()
    {
        return implode(' ', array($this->first_name, $this->last_name)) . (" ({$this->username})");
    }

    /**
     * @return string
     */
    public function getReversedFullName()
    {
        return implode(' ', array($this->last_name, $this->first_name));
    }

    /**
     * @return string
     */
    public function getReversedFullNameAndUserName()
    {
        return implode(' ', array($this->last_name, $this->first_name)) . (" ({$this->username})");
    }

    /**
     * @return string
     */
    public function getFullNameAndTitle()
    {
        return implode(' ', array($this->title, $this->first_name, $this->last_name));
    }

    /**
     * @return string
     */
    public function getFirstInitialFullNameAndTitle()
    {
        return implode(' ', array($this->title, strtoupper($this->first_name[0]), $this->last_name));
    }

    /**
     * @return string
     */
    public function getFullNameAndTitleAndQualifications()
    {
        return implode(' ', array(
            $this->title,
            $this->first_name,
            $this->last_name,
        )) . ($this->qualifications ? ' ' . $this->qualifications : '');
    }

    /**
     * @return string
     */
    public function getReversedFullNameAndTitle()
    {
        return implode(' ', array($this->title, $this->last_name, $this->first_name));
    }

    /**
     * Returns the users that are eligible to be considered surgeons.
     *
     * @return User[] List of surgeon users
     */
    public static function getSurgeons()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('is_surgeon', 1);
        $criteria->compare('active', 1);
        $criteria->order = 'last_name,first_name asc';

        return self::model()->findAll($criteria);
    }

    /**
     * Perform an audit log for the user
     *
     * @param       $target
     * @param       $action
     * @param null  $data
     * @param bool  $log
     * @param array $properties
     */
    public function audit($target, $action, $data = null, $log = false, $properties = array())
    {
        $properties['user_id'] = $this->id;
        parent::audit($target, $action, $data, $log, $properties);
    }

    public function getListSurgeons()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('is_surgeon', 1);
        $criteria->compare('active', 1);
        $criteria->order = 'last_name,first_name asc';

        return CHtml::listData(self::model()->findAll($criteria), 'id', 'reversedFullName');
    }

    public function getReportDisplay()
    {
        return $this->fullName;
    }

    public function getIs_local()
    {
        return in_array($this->username, \Yii::app()->params['local_users']);
    }

    public function generateRandomPassword()
    {
        $pwd = bin2hex(openssl_random_pseudo_bytes(15));
        $pwd[rand(0, strlen($pwd))] = "_";

        return $pwd;
    }

    public function beforeValidate()
    {
        //When LDAP is enabled and the user is not a local user than we generate a random password

        if ($this->isNewRecord && \Yii::app()->params['auth_source'] === 'LDAP' && !$this->is_local) {
            $password = $this->generateRandomPassword();
            $this->password = $password;
            $this->password_repeat = $password;
        } elseif ($this->isNewRecord && (\Yii::app()->params['auth_source'] === 'SAML' || \Yii::app()->params['auth_source'] === 'OIDC')) {
            $password = $this->generateRandomPassword();
            $this->password = $password;
            $this->password_repeat = $password;
        }

        if (!$this->password_hashed) {
            if ($this->password != $this->password_repeat) {
                $this->addError('password', 'Password confirmation must match exactly');
            }
            $this->salt = $this->randomSalt();
        }

        if ($this->getIsNewRecord() && !$this->password) {
            $this->addError('password', 'Password is required');
        }

        return parent::beforeValidate();
    }

    public function randomSalt()
    {
        $salt = '';
        for ($i = 0; $i < 10; ++$i) {
            switch (rand(0, 2)) {
                case 0:
                    $salt .= chr(rand(48, 57));
                    break;
                case 1:
                    $salt .= chr(rand(65, 90));
                    break;
                case 2:
                    $salt .= chr(rand(97, 122));
                    break;
            }
        }

        return $salt;
    }

    public function findAsContacts($term)
    {
        $contacts = array();

        $criteria = new CDbCriteria();
        $criteria->addSearchCondition('lower(`t`.last_name)', $term, false);
        $criteria->compare('active', 1);
        $criteria->order = 'contact.title, contact.first_name, contact.last_name';

        foreach (self::model()->with(array('contact' => array('with' => 'locations')))->findAll($criteria) as $user) {
            foreach ($user->contact->locations as $location) {
                $contacts[] = array(
                    'line' => $user->contact->contactLine($location),
                    'contact_location_id' => $location->id,
                );
            }
        }

        return $contacts;
    }

    public function getActiveSiteSelections()
    {
        return array_filter($this->siteSelections, function ($site) {
            return $site->active;
        });
    }

    public function getNotSelectedSiteList()
    {
        $site_ids = array();
        foreach ($this->siteSelections as $site) {
            $site_ids[] = $site->id;
        }

        $criteria = new CDbCriteria();
        $criteria->compare('institution_id', Institution::model()->getCurrent()->id);
        $criteria->compare('active', 1);
        $criteria->addNotInCondition('id', $site_ids);
        $criteria->order = 'name asc';

        return Site::model()->findAll($criteria);
    }

    public function getNotSelectedFirmList()
    {
        $firms = Yii::app()->db->createCommand()
            ->select('f.id, f.name, s.name AS subspecialty')
            ->from('firm f')
            ->leftJoin('service_subspecialty_assignment ssa', 'f.service_subspecialty_assignment_id = ssa.id')
            ->leftJoin('subspecialty s', 'ssa.subspecialty_id = s.id')
            ->leftJoin('user_firm uf', 'uf.firm_id = f.id and uf.user_id = ' . Yii::app()->user->id)
            ->where('uf.id is null and f.active = 1')
            ->order('f.name, s.name')
            ->queryAll();
        $data = array();
        foreach ($firms as $firm) {
            if ($firm['subspecialty']) {
                $data[$firm['id']] = $firm['name'] . ' (' . $firm['subspecialty'] . ')';
            } else {
                $data[$firm['id']] = $firm['name'];
            }
        }
        natcasesort($data);

        return $data;
    }

    /**
     * @return CAuthItem[]
     */
    public function getRoles()
    {
        return $this->id ? Yii::app()->authManager->getRoles($this->id) : array();
    }

    /**
     * @param string[] $roles
     */
    public function saveRoles(array $roles)
    {
        $old_roles = array_map(function ($role) {
            return $role->name;
        }, $this->roles);
        $added_roles = array_diff($roles, $old_roles);
        $removed_roles = array_diff($old_roles, $roles);

        foreach ($added_roles as $role) {
            Yii::app()->authManager->assign($role, $this->id);
//            If one of the roles added is an admin, then provide the user with permissions to manage all trials - CERA -523
            if ($role == 'admin') {
                $trials = Trial::model()->findAll();
                foreach ($trials as $trial) {
                    $newPermission = new UserTrialAssignment();
                    $newPermission->user_id = $this->id;
                    $newPermission->trial_id = $trial->id;
                    $newPermission->trial_permission_id = TrialPermission::model()->find('code = ?', array('MANAGE'))->id;
                    $criteria = new CDbCriteria();
                    $criteria->condition = 'user_id=:user_id AND trial_id=:trial_id AND trial_permission_id=:trial_permission_id';
                    $criteria->params = array(':user_id'=>$this->id,':trial_id'=>$trial->id,':trial_permission_id'=>$newPermission->trial_permission_id );
                    if (UserTrialAssignment::model()->exists($criteria) == false) {
                        if (!$newPermission->save()) {
                            throw new CHttpException(500, 'The owner permission for the new trial could not be saved: '
                                . print_r($newPermission->getErrors(), true));
                        }
                    }
                }
            }
        }

        foreach ($removed_roles as $role) {
            Yii::app()->authManager->revoke($role, $this->id);
//            If one of the roles removed from the user is that of an admin, thhn remove ability to manage trials not owned by the user - CERA-523
            if ($role == 'admin') {
                $trials = Trial::model()->findAll();
                foreach ($trials as $trial) {
                    $criteria = new CDbCriteria();
                    $criteria->condition = 'user_id=:user_id AND trial_id=:trial_id AND trial_permission_id=:trial_permission_id AND role IS NULL AND is_principal_investigator=:is_principal_investigator AND is_study_coordinator=:is_study_coordinator';
                    $criteria->params = array(':user_id'=>$this->id,':trial_id'=>$trial->id,':trial_permission_id'=>TrialPermission::model()->find('code = ?', array('MANAGE'))->id,':is_principal_investigator'=>0,':is_study_coordinator'=>0 );
                    if (UserTrialAssignment::model()->exists($criteria)) {
                        if (!UserTrialAssignment::model()->deleteAll($criteria)) {
                            throw new CHttpException(500, 'The user permissions for this trial could not be removed: '
                                . print_r(UserTrialAssignment::model()->getErrors(), true));
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $firms
     * @throws CDbException
     * @throws FirmSaveException
     */
    public function saveFirms(array $firms)
    {
        if (!$this->global_firm_rights && count($firms) === 0) {
            throw new FirmSaveException('When global firm rights are not set, a firm must be selected');
        }

        $transaction = Yii::app()->db->beginTransaction();
        FirmUserAssignment::model()->deleteAll('user_id = :user_id', array('user_id' => $this->id));
        foreach ($firms as $firm) {
            $firmUserAssign = new FirmUserAssignment();
            $firmUserAssign->user_id = $this->id;
            $firmUserAssign->firm_id = $firm;
            if (!$firmUserAssign->insert()) {
                throw new CDbException('Unable to save firm assignment');
            }
        }
        $transaction->commit();
    }

    /**
     * Return all firms that the user has access rights to
     *
     * @return Firm[]
     */
    public function getAvailableFirms()
    {
        $crit = new CDbCriteria;
        $crit->compare('active', 1);
        if (!$this->global_firm_rights) {
            $crit->join = "left join firm_user_assignment fua on fua.firm_id = t.id and fua.user_id = :user_id " .
                "left join user_firm_rights ufr on ufr.firm_id = t.id and ufr.user_id = :user_id " .
                "left join service_subspecialty_assignment ssa on ssa.id = t.service_subspecialty_assignment_id " .
                "left join user_service_rights usr on usr.service_id = ssa.service_id and usr.user_id = :user_id ";
            $crit->addCondition("fua.id is not null or ufr.id is not null or usr.id is not null");
            $crit->params['user_id'] = $this->id;
        }

        return Firm::model()->findAll($crit);
    }

    /**
     * @return array
     */
    public function getAllConsultants($subspecialty = null)
    {
        $consultant_names = User::model()->findAll(array('condition' => 'is_consultant = 1', 'order' => 'first_name asc'), 'id', 'first_name');
        $consultant_name = array();
        $i = 0;
        foreach ($consultant_names as $consultant) {
            $consultant_name[$i]['id'] = $consultant->id;
            $consultant_name[$i]['name'] = $consultant->getFullName();
            $i++;
        }
        return $consultant_name;
    }

    /**
     * Get the portal user if it exists.
     *
     * @return CActiveRecord
     */
    public function portalUser()
    {
        $username = (array_key_exists(
            'portal_user',
            Yii::app()->params
        )) ? Yii::app()->params['portal_user'] : 'portal_user';
        $crit = new CDbCriteria();
        $crit->compare('username', $username);

        return $this->find($crit);
    }

    /**
     * @return bool
     */
    public function checkSignature()
    {
        return ($this->signature_file_id) ? true : false;
    }

    /**
     * @param $text
     * @param $key
     * @return string|null
     */
    protected function decryptSignature($text, $key)
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypt = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($text), MCRYPT_MODE_ECB, $iv));
        if (Yii::app()->params['no_md5_verify']) {
            return $decrypt;
        }

        return Helper::md5Verified($decrypt);
    }

    /**
     * @param $uniqueCodeId
     * @return string
     */
    public function generateUniqueCodeWithChecksum($uniqueCodeId)
    {
        $uniqueCode = UniqueCodes::model()->findByPk($uniqueCodeId)->code;
        $salt = (isset(Yii::app()->params['portal']['credentials']['client_id'])) ? Yii::app()->params['portal']['credentials']['client_id'] : '';
        $check_digit1 = new CheckDigitGenerator(Yii::app()->params['institution_code'] . $uniqueCode, $salt);
        $check_digit2 = new CheckDigitGenerator($uniqueCode . Yii::app()->user->id, $salt);
        $finalUniqueCode = Yii::app()->params['institution_code'] . $check_digit1->generateCheckDigit() . '-' . $uniqueCode . '-' . $check_digit2->generateCheckDigit();

        return $finalUniqueCode;
    }

    /**
     * @return mixed
     */
    protected function getUniqueCode()
    {
        $userUniqueCode = UniqueCodeMapping::model()->findByAttributes(array('user_id' => $this->id));

        return $userUniqueCode->unique_code_id;
    }

    /**
     * @return array
     */
    protected function getPasswordRestrictions()
    {
        $pw_restrictions = Yii::app()->params['pw_restrictions'];

        if ($pw_restrictions===null) {
            $pw_restrictions = array(
                'min_length' => 8,
                'min_length_message' => 'Passwords must be at least 8 characters long',
                'max_length' => 70,
                'max_length_message' => 'Passwords must be at least 70 characters long',
                'strength_regex' => '%^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\W]).*$%',
                'strength_message' => 'Passwords must include an upper case letter, a lower case letter, a number, and a special character'
            );
        }
        if (!isset($pw_restrictions['min_length'])) {
            $pw_restrictions['min_length'] = 8;
        }
        if (!isset($pw_restrictions['min_length_message'])) {
            $pw_restrictions['min_length_message'] = 'Passwords must be at least '.$pw_restrictions['min_length'].' characters long';
        }
        if (!isset($pw_restrictions['max_length'])) {
            $pw_restrictions['max_length'] = 70;
        }
        if (!isset($pw_restrictions['max_length_message'])) {
            $pw_restrictions['max_length_message'] = 'Passwords must be at most '.$pw_restrictions['max_length'].' characters long';
        }
        if (!isset($pw_restrictions['strength_regex'])) {
            $pw_restrictions['strength_regex'] = "%.*%";
        }
        if (!isset($pw_restrictions['strength_message'])) {
            $pw_restrictions['strength_message'] = "N/A";
        }
        return $pw_restrictions;
    }

    /**
     * @param $signature_pin
     * @return bool|string
     */
    public function getDecryptedSignature($signature_pin)
    {
        if ($signature_pin) {
            if ($this->signature_file_id) {
                $signature_file = ProtectedFile::model()->findByPk($this->signature_file_id);
                $image_data = base64_decode(
                    $this->decryptSignature(
                        file_get_contents($signature_file->getPath()),
                        md5(md5($this->id) . $this->generateUniqueCodeWithChecksum($this->getUniqueCode()) . $signature_pin)
                    )
                );

                if (strlen($image_data) > 100) {
                    return $image_data;
                }
            }
        }

        return false;
    }

    /**
     * Returns users who can access the roles in the given param
     *
     * @param array $roles
     * @param bool $return_models
     * @return array user ids or array of User models
     */
    public function findAllByRoles(array $roles, $return_models = false)
    {
        $user_ids = array();
        $users_with_roles = array();

        $users = Yii::app()->db->createCommand("SELECT DISTINCT(userid) FROM `authassignment` WHERE `itemname` IN ('" . (implode("','", $roles)) . "')")->queryAll();

        foreach ($users as $index => $user) {
            $user_ids[] = $user['userid'];
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition('t.id', $user_ids);

        if ( !empty($user_ids)) {
            $users = $this->findAll($criteria);

            foreach ($users as $id => $user) {
                foreach ($roles as $role) {
                    if (Yii::app()->authManager->checkAccess($role, $user->id)) {
                        $users_with_roles[$user->id] = $return_models ? $user : $user->id;
                    }
                }
            }
        }

        return $users_with_roles;
    }

    /**
     * Returns active status for a selected user
     *
     * @param User $user
     * @return bool active status for a user
     */
    public function getUserActiveStatus($user)
    {
        if ($user->active) {
            $active = '1';
        } else {
            $active = '0';
        }
        return $active;
    }

     /**
     * Returns if user has that a password status
     *
     * @param string $status
     * @param User $user
     * @return bool is user at that level
     */
    public function testUserPWStatus($status = 'locked', $user = null)
    {
        if (!$user) {
            $user = $this;
        }
        if ($user->password_status == $status) {
            return true;
        }
        if ($status === 'locked') { // checking bad statuses
            if (!($user->password_status === 'current' || $user->password_status === 'expired' ||$user->password_status === 'stale' )) {
                return true;
            }
        }
        return false;
    }
    /**
     * Returns if setting the password status was successful/if it would be if $save had been true, assuming the save performs.
     *
     * @param string $status
     * @param User $user
     * @param bool $save should the function save the value itself?
     * @return bool is user at that level or greater - if true and saving were there issues saving? false = "I cannot do that" either due to rules or error saving (like the value is already set to that)
     */
    public function setPWStatusHarsher($status = null, $user = null, $save = true)
    {
        if (!$user) {
            $user = $this;
        }
        switch ($status) {
            case 'locked':
                $user->password_status ='locked';
                if ($save) {
                    return $user->saveAttributes(array('password_status'));
                } else {
                    return true;
                }
                break;
            case 'softlocked':
                if ($user->password_status !='locked') {
                    $user->password_status ='softlocked';
                    $temp_now = new DateTime();
                    $pw_timeout = !empty(Yii::app()->params['pw_status_checks']['pw_softlock_timeout'])? Yii::app()->params['pw_status_checks']['pw_softlock_timeout'] : '10 mins';
                    $user->password_softlocked_until = date_format(date_add($temp_now, date_interval_create_from_date_string($pw_timeout)), "Y-m-d H:i:s");
                    if ($save) {
                        return $user->saveAttributes(array('password_status', 'password_softlocked_until'));
                    } else {
                        return true;
                    }
                }
                break;
            case 'expired':
                if ($user->password_status === 'current'||$user->password_status === 'stale') {
                    $user->password_status ='expired';
                    if ($save) {
                        return $user->saveAttributes(array('password_status'));
                    } else {
                        return true;
                    }
                }
                break;
            case 'stale':
                if ($user->password_status === 'current') {
                    $user->password_status ='stale';
                    if ($save) {
                        return $user->saveAttributes(array('password_status'));
                    } else {
                        return true;
                    }
                }
                break;
        }
        return false;
    }

    /**
     * Checks if the user has passed the allowed log in attempts, and will apply the appropriate status if so.
     *
     * @param User $user
     */
    public function setFailedLogin($user = null)
    {
        if (!$user) {
            $user = $this;
        }
        if (!$user->testUserPWStatus()) {
            //Increase the number of failed tries
            $user->password_failed_tries++;
            $user->saveAttributes(array('password_failed_tries'));
        }
    }

    /**
     * Checks if the user has passed the allowed log in attempts, and will apply the appropriate status if so.
     *
     * @param User $user
     * @return bool has status level been changed?
     */
    public function userLogOnAttemptsCheck($user = null)
    {
        if (!$user) {
            $user = $this;
        }
        $threshold = isset(Yii::app()->params['pw_status_checks']['pw_tries'])?Yii::app()->params['pw_status_checks']['pw_tries']:3;
        if ($threshold) { //only check pw tries if we have a threshold to check against
            $pwTriesFailed = Yii::app()->params['pw_status_checks']['pw_tries_failed']?? 'locked';
            if ($pwTriesFailed === 'softlocked' && $user->password_status === 'softlocked' ) {
                if ( $user->password_softlocked_until < date("Y-m-d H:i:s")) {
                    $user->password_failed_tries = 0;
                    $user->password_status = 'current';
                    $user->saveAttributes(array('password_status', 'password_failed_tries', 'password_softlocked_until'));
                    $user->audit('login', 'user-soft-unlock', null, "User: {$this->username} has finished their softlock period ");
                }
            }

            if ($user->password_failed_tries >= $threshold) {   // if the number of attempts is greater than what is allowed then try to lock the account
                $user->password_failed_tries = $threshold; //reset to avoid overflow errors
                if ($user->setPWStatusHarsher($pwTriesFailed, $user)) {
                    $user->audit('login', 'user-' . $pwTriesFailed, null, "User: {$this->username} has exceeded " . $threshold.' tries, account is now ' . $pwTriesFailed);
                } else {
                    $user->audit('login', 'user-' . $pwTriesFailed.'-same', null, "User: {$this->username} has exceeded " . $threshold.' tries, account is already ' . $pwTriesFailed);
                }
                return $user->saveAttributes(array('password_failed_tries')); // save only these values
            }
        }
        return false;
    }

    /**
     * Checks if the user has the time allowed for changing thier password, and will apply the appropriate status if so.
     *
     * @param User $user
     * @return bool has status level been changed?
     */
    public function testUserPwDate($date = null, $user = null)
    {
        if (!$user) {
            $user = $this;
        }
        if ($date == null) {
            $date = $this->password_last_changed_date;
        }
        if ($date == null) {
            $date = date("Y-m-d H:i:s");
        }
        //Get params
        $pwDaysLock = !empty(Yii::app()->params['pw_status_checks']['pw_days_lock']) ? Yii::app()->params['pw_status_checks']['pw_days_lock'] : null; //get tolerance for pw expiry
        $pwDaysExpire = !empty(Yii::app()->params['pw_status_checks']['pw_days_expire']) ? Yii::app()->params['pw_status_checks']['pw_days_expire'] : null ; //get tolerance for pw expiry
        $pwDaysStale = !empty(Yii::app()->params['pw_status_checks']['pw_days_stale']) ? Yii::app()->params['pw_status_checks']['pw_days_stale'] : null; //get tolerance for pw expiry

        if ($pwDaysLock && $user->password_last_changed_date) {
            $pwDateCutoffLock =  date("Y-m-d H:i:s", strtotime('-'.$pwDaysLock)); // get last valid time
            if ($date <= $pwDateCutoffLock) {
                return $user->setPWStatusHarsher('locked');
            }
        }
        if ($pwDaysExpire) {
            $pwDateCutoffExpire =  date("Y-m-d H:i:s", strtotime('-'.$pwDaysExpire)); // get last valid time
            if ($date <= $pwDateCutoffExpire) {
                return $user->setPWStatusHarsher('expired');
            }
        }
        if ($pwDaysStale) {
            $pwDateCutoffStale =  date("Y-m-d H:i:s", strtotime('-'.$pwDaysStale)); // get last valid time
            if ($date <= $pwDateCutoffStale) {
                return $user->setPWStatusHarsher('stale');
            }
        }
        return false;
    }

    /**
     * Gets the frontend name of the password status
     * @param string $user
     * @return string Name of status
     */
    public function getUserPwStatusName($user = null)
    {
        if (!$user) {
            $user = $this;
        }
        switch ($user->password_status) {
            case 'current':
                return 'Current';
                break;
            case 'stale':
                return 'Stale';
                break;
            case 'expired':
                return 'Expired';
                break;
            case 'softlocked':
                return 'Soft locked with timeout';
                break;
            default:
                return 'Locked';
                break;
        }
    }
        /**
     * Gets the frontend name of the password status
     * @param string $user
     * @return string Name of status
     */
    public function getUserDaysLeft($user = null)
    {
        if (!$user) {
            $user = $this;
        }
        $daysLeft = array();
        if ($user->password_last_changed_date) {
            $pwDaysStale = !empty(Yii::app()->params['pw_status_checks']['pw_days_stale'])?Yii::app()->params['pw_status_checks']['pw_days_stale'] : null; //get tolerance for pw expiry
            $pwDaysExpire = !empty(Yii::app()->params['pw_status_checks']['pw_days_expire'])?Yii::app()->params['pw_status_checks']['pw_days_expire'] : null; //get tolerance for pw expiry
            $pwDaysLock = !empty(Yii::app()->params['pw_status_checks']['pw_days_lock'])?Yii::app()->params['pw_status_checks']['pw_days_lock'] : null; //get tolerance for pw expiry

            if ($pwDaysStale) {
                $daysLeft["DaysStale"]=date_diff(date_create(date("Y-m-d H:i:s", strtotime('-'.$pwDaysStale))), date_create($user->password_last_changed_date))->format('%a days');
            }
            if ($pwDaysExpire) {
                $daysLeft["DaysExpire"]=date_diff(date_create(date("Y-m-d H:i:s", strtotime('-'.$pwDaysExpire))), date_create($user->password_last_changed_date))->format('%a days');
            }
            if ($pwDaysLock) {
                $daysLeft["DaysLock"]=date_diff(date_create(date("Y-m-d H:i:s", strtotime('-'.$pwDaysLock))), date_create($user->password_last_changed_date))->format('%a days');
            }
        }
        return $daysLeft;
    }

    public function setSSOUserInformation($response)
    {
        // Set user credentials that login through SAML authentication
        if (Yii::app()->params['auth_source'] === 'SAML') {
            $this->username = $response['username'][0];
            $this->first_name = $response['FirstName'][0];
            $this->last_name = $response['LastName'][0];
            $this->email = $response['username'][0];   // For SAML users, email would be their username
            $this->title = array_key_exists('title', $response) ? $response['title'][0] : '';
            $this->qualifications = array_key_exists('qualifications', $response) ? $response['qualifications'][0] : '';
            $this->role = array_key_exists('role', $response) ? $response['role'][0] : '';
        }
        // Set the user credentials that login through OIDC suthentication
        elseif (Yii::app()->params['auth_source'] === 'OIDC') {
            $this->username = $response['email'];       // OIDC users set emails as their usernames
            $this->first_name = $response['given_name'];
            $this->last_name = $response['family_name'];
            $this->email = $response['email'];
            $this->title = array_key_exists('title', $response) ? $response['title'] : '';
            $this->qualifications = array_key_exists('qualifications', $response) ? $response['qualifications'] : '';
            $this->role = array_key_exists('position', $response) ? $response['position'] : '';
            $this->doctor_grade_id = array_key_exists('doctor_grade', $response) ? DoctorGrade::model()->find("grade = :grade", [':grade' => $response['doctor_grade']])->id : '';
            $this->registration_code = array_key_exists('registration_code', $response) ? $response['registration_code'] : '';
            $this->is_consultant = array_key_exists('consultant', $response) && strtolower($response['consultant']) === 'yes' ? 1 : 0;
            $this->is_surgeon = array_key_exists('surgeon', $response) && strtolower($response['surgeon']) === 'yes' ? 1 : 0;
        }

        // Set the user active regardless
        $this->active = true;
        $this->setdefaultSSORights();

        $defaultRights = SsoDefaultRights::model()->findByAttributes(['source' => 'SSO']);
        $user = self::model()->find('username = :username', array(':username' => $this->username));
        //If the user is logging into the OE for the first time, assign default roles and firms
        if ($user === null) {
            $this->save();
            $this->id = self::model()->find('username = :username', array(':username' => $this->username))->id;

            $this->setdefaultSSOFirms();
            $this->setdefaultSSORoles();
        } else {
            $this->id = $user->id;
        }
        // Roles from the token need to be assigned to the user after every login
        if (!$defaultRights['default_enabled']) {
            // Pass the array of roles from the token
            $this->setRolesFromSSOToken($response['roles']);
        }

        return array(
            'username' => $this->username,
            'password' => $this->password
        );
    }

    public function setdefaultSSORights()
    {
        $defaultRights = SsoDefaultRights::model()->findByAttributes(['source' => 'SSO']);
        $this->global_firm_rights = $defaultRights['global_firm_rights'];
        // If global firm rights have been provided then no need to select firms and vice versa
        $this->has_selected_firms = !$this->global_firm_rights;
    }

    public function setdefaultSSOFirms()
    {
        $ssoFirms = SsoDefaultFirms::model()->findAll();
        $defaultFirms = array();
        foreach ($ssoFirms as $ssoFirm) {
            $defaultFirms[] = $ssoFirm['firm_id'];
        }
        $this->saveFirms($defaultFirms);
    }

    public function setdefaultSSORoles()
    {
        $ssoRoles = SsoDefaultRoles::model()->findAll();
        $defaultRoles = array();
        foreach ($ssoRoles as $ssoRole) {
            $defaultRoles[] = $ssoRole['roles'];
        }
        $this->saveRoles($defaultRoles);
    }

    public function setRolesFromSSOToken($roles)
    {
        $assignedRoles = array();
        foreach ($roles as $role) {
            $userRoles = SsoRoles::model()->find("name = :role", [':role' => $role]);
            if (!$userRoles) {
                $this->audit('SsoRoles', 'assign-role', 'SSO Role "' . $role . '" not found for user ' . $this->username);
                if (Yii::app()->params['strict_SSO_roles_check']) {
                    $this->audit('SsoRoles', 'login-failed', "SSO Role not found so cannot login: $this->username", true);
                    throw new Exception('The role "' . $role . '" was not found in OpenEyes');
                }
            } else {
                foreach ($userRoles->sso_roles_assignment as $userRole) {
                    if (!in_array($userRole->authitem_role, $assignedRoles, true)) {
                        $assignedRoles[] = $userRole->authitem_role;
                    }
                }
            }
        }
        $this->saveRoles($assignedRoles);
    }
}
