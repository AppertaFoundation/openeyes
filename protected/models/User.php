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
 */
class User extends BaseActiveRecordVersioned
{
    /**
     * Used to check password and password confirmation match.
     *
     * @var string
     */
    public $password_repeat;

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
            array(
                'username, first_name, last_name, email, active, global_firm_rights, title, qualifications, role, salt, password, is_consultant, is_surgeon,
                 has_selected_firms,doctor_grade_id, registration_code, signature_file_id',
                'safe',
            ),
        );

        if (Yii::app()->params['auth_source'] == 'BASIC') {
            $user = Yii::app()->request->getPost('User');

            if (isset($user['is_surgeon']) && $user['is_surgeon'] == 1) {
                return array_merge(
                    $commonRules,
                    array(
                        array(
                            'username',
                            'match',
                            'pattern' => '/^[\w|\.\-_\+@]+$/',
                            'message' => 'Only letters, numbers and underscores are allowed for usernames.',
                        ),
                        array('username, email, first_name, last_name, active, global_firm_rights,doctor_grade_id,registration_code ','required',),
                        array('username, password, first_name, last_name', 'length', 'max' => 40),
                        array(
                            'password',
                            'length',
                            'min' => 5,
                            'message' => 'Passwords must be at least 6 characters long.',
                        ),
                        array('email', 'length', 'max' => 80),
                        array('email', 'email'),
                        array('salt', 'length', 'max' => 10),
                        // Added for password comparison functionality
                        array('password_repeat', 'safe'),
                    )
                );

            } else {
                return array_merge(
                    $commonRules,
                    array(
                        array(
                            'username',
                            'match',
                            'pattern' => '/^[\w|\.\-_\+@]+$/',
                            'message' => 'Only letters, numbers and underscores are allowed for usernames.',
                        ),
                        array('username, email, first_name, last_name, active, global_firm_rights', 'required'),
                        array('username, password, first_name, last_name', 'length', 'max' => 40),
                        array(
                            'password',
                            'length',
                            'min' => 5,
                            'message' => 'Passwords must be at least 6 characters long.',
                        ),
                        array('email', 'length', 'max' => 80),
                        array('email', 'email'),
                        array('salt', 'length', 'max' => 10),
                        // Added for password comparison functionality
                        array('password_repeat', 'safe'),
                    )
                );
            }

        } elseif (Yii::app()->params['auth_source'] == 'LDAP') {
            return array_merge(
                $commonRules,
                array(
                    array('username, active, global_firm_rights', 'required'),
                    array('username', 'length', 'max' => 40),
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
        return array(
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
            'preferred_firms' => array(
                self::HAS_MANY,
                'Firm',
                'firm_id',
                'through' => 'firm_preferences',
                'order' => 'firm_preferences.position DESC',
                'limit' => 6,
            ),
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
    }


    /**
     * @return mixed|null
     * @deprecated - since v2.2
     */
    public function getIs_doctor(){
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
        $preference = UserFirmPreference::model()->find('user_id = :user_id AND firm_id = :firm_id',
            array(':user_id' => $this->id, ':firm_id' => $firm_id));
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
            'is_consultant' => 'Consultant',
            'is_surgeon' => 'Surgeon',
            'doctor_grade_id' => 'Grade',
            'role' => 'Position'
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
     * Saves or updates a db record and creates the salt for a new record of
     *    authentication type 'basic'.
     *
     * @return bool
     */
    public function save($runValidation = true, $attributes = null, $allow_overriding = false, $save_archive = false)
    {
        if (Yii::app()->params['auth_source'] == 'BASIC') {
            /*
             * AUTH_BASIC requires creation of a salt. AUTH_LDAP doesn't.
             */
            if ($this->getIsNewRecord() && !$this->salt) {
                $salt = '';
                $possible = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

                for ($i = 0; $i < 10; ++$i) {
                    $salt .= $possible[mt_rand(0, strlen($possible) - 1)];
                }

                $this->salt = $salt;
            }
        }

        return parent::save($runValidation, $attributes, $allow_overriding, $save_archive);
    }

    /**
     * Hashes the user password for insertion into the db.
     */
    protected function afterValidate()
    {
        parent::afterValidate();

        if (!preg_match('/^[0-9a-f]{32}$/', $this->password)) {
            $this->password = $this->hashPassword($this->password, $this->salt);
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
        return md5($salt . $password);
    }

    /**
     * Returns whether the password provided is valid for this user.
     *
     * Hashes the password with the salt for this user. If valid, return true,
     * else return false.
     *
     * @param string $password
     *
     * @return bool
     */
    public function validatePassword($password)
    {
        return $this->hashPassword($password, $this->salt) === $this->password;
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
        if($this->isNewRecord && \Yii::app()->params['auth_source'] == 'LDAP' && !$this->is_local){
            $password = $this->generateRandomPassword();
            $this->password = $password;
            $this->password_repeat = $password;
        }

        if (!preg_match('/^[0-9a-f]{32}$/', $this->password)) {
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
        }

        foreach ($removed_roles as $role) {
            Yii::app()->authManager->revoke($role, $this->id);
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
        $username = (array_key_exists('portal_user',
            Yii::app()->params)) ? Yii::app()->params['portal_user'] : 'portal_user';
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

        foreach($users as $index => $user){
            $user_ids[] = $user['userid'];
        }

        $criteria = new CDbCriteria();
        $criteria->addInCondition('t.id', $user_ids);

        if( !empty($user_ids)){
            $users = $this->findAll($criteria);

            foreach($users as $id => $user) {
                foreach($roles as $role){
                    if(Yii::app()->authManager->checkAccess($role, $user->id)) {
                        $users_with_roles[$user->id] = $return_models ? $user : $user->id;
                    }
                }
            }
        }

        return $users_with_roles;
    }
}
