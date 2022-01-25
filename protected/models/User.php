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
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property int $global_firm_rights
 * @property int $correspondence_sign_off_user_id
 * @property string $correspondence_sign_off_text
 *
 * @property User $signOffUser
 */
class User extends BaseActiveRecordVersioned
{
    const PIN_REGEN_LIMIT = 5;
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
            array('id, first_name, last_name, email, global_firm_rights, correspondence_sign_off_user_id', 'safe', 'on' => 'search'),
            array('title, first_name, last_name', 'match', 'pattern' => '/^[a-zA-Z]+(([\',. -][a-zA-Z ])?[a-zA-Z]*)*$/', 'message' => 'Invalid {attribute} entered.'),
            array('correspondence_sign_off_text', 'required'),
            array('correspondence_sign_off_text', 'length', 'max' => 255),
            array(
                'first_name, last_name, email, global_firm_rights, title, qualifications, role, is_consultant, is_surgeon,
                 has_selected_firms,doctor_grade_id, registration_code, signature_file_id, correspondence_sign_off_user_id',
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

        $generalUserRules = [
            ['email, first_name, last_name, global_firm_rights', 'required'],
            ['first_name, last_name', 'length', 'max' => 40],
            ['email', 'length', 'max' => 80],
            ['email', 'email']
        ];

        $surgeonRules = [
            ['doctor_grade_id, registration_code', 'required']
        ];

        if (isset($user['is_surgeon']) && $user['is_surgeon'] == 1) {
            $commonRules = array_merge($commonRules, $surgeonRules);
        }

        return array_merge(
            $commonRules,
            $generalUserRules
        );
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
            'signOffUser' => array(self::BELONGS_TO, 'User', 'correspondence_sign_off_user_id'),
            'authentications' => array(self::HAS_MANY, 'UserAuthentication', 'user_id'),
            'pincode' => array(self::HAS_ONE, 'UserPincode', 'user_id'),
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

    public function getHieAccessLevel()
    {
        $hie_roles = [
            'HIE - Extended' => 'Level 4 - Extended',
            'HIE - Summary' => 'Level 3 - Summary',
            'HIE - Admin' => 'Level 2 - Admin',
            'HIE - View' => 'Level 1 - Default View'
        ];

        $highest_role = null;

        foreach ($hie_roles as $key => $value) {
            if (Yii::app()->authManager->checkAccess($key, Yii::app()->user->id)) {
                $highest_role = $value;
                break;
            }
        }

        return $highest_role;
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

    public function init()
    {
        parent::init();
        if ($this->isNewRecord) {
            $this->global_firm_rights = 1;
        }
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email' => 'Email',
            'global_firm_rights' => 'Global firm rights',
            'firms' => 'Context',
            'is_consultant' => 'Consultant',
            'is_surgeon' => 'Surgeon',
            'doctor_grade_id' => 'Grade',
            'role' => 'Position',
            'correspondence_sign_off_text' => 'Correspondence sign-off text'
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
        $criteria->compare('first_name', $this->first_name, true);
        $criteria->compare('last_name', $this->last_name, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('global_firm_rights', $this->global_firm_rights);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
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
    public function getReversedFullName()
    {
        return implode(' ', array($this->last_name, $this->first_name));
    }

    /**
     * @param $institution_id
     * @return string
     * @throws Exception
     */
    public function getReversedNameAndInstitutionUsername($institution_id): string
    {
        $return = implode(' ', array($this->last_name, $this->first_name));

        $user_auth_id = Yii::app()->db->createCommand()
            ->select('ua.id')
            ->from('institution_authentication ia')
            ->join('user_authentication ua', 'ua.institution_authentication_id = ia.id')
            ->where('ia.institution_id = :institution_id AND ua.user_id = :user_id')
            ->limit(1)
            ->bindValues([':institution_id' => $institution_id, ':user_id' => $this->id])
            ->queryScalar();

        // Assuming that, despite multiple institution authentications,
        // a user's username is identical for all of them.
        $user_auth = UserAuthentication::model()->findByPk($user_auth_id);

        if (!$user_auth) {
            throw new Exception('User authentication not found for institution ' . $institution_id);
        }
        return $return . " ($user_auth->username)";
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

    public function getFirmsForCurrentInstitution()
    {
        return array_filter($this->getAvailableFirms(), static function ($item) {
            return Yii::app()->session['selected_institution_id'] === $item->institution_id;
        });
    }

    public function getInitials()
    {
        return mb_strtoupper($this->first_name[0]) . mb_strtoupper($this->last_name[0]);
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

    public function getUsersFromCurrentInstitution()
    {
        $criteria = new CDbCriteria();
        $criteria->join .= "join user_authentication ua on ua.user_id = t.id";
        $criteria->join .= " join institution_authentication ia on ua.institution_authentication_id = ia.id";
        $criteria->compare('ia.institution_id', \Yii::app()->session['selected_institution_id']);
        $criteria->order = 't.last_name,t.first_name asc';

        return self::model()->findAll($criteria);
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
        $criteria->order = 'last_name,first_name asc';

        return self::model()->findAll($criteria);
    }

    /**
     * Perform an audit log for the user
     *
     * @param       $target
     * @param       $action
     * @param null $data
     * @param bool $log
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
        $criteria->order = 'last_name,first_name asc';

        return CHtml::listData(self::model()->findAll($criteria), 'id', 'reversedFullName');
    }

    public function getReportDisplay()
    {
        return $this->fullName;
    }

    public function findAsContacts($term)
    {
        $contacts = array();

        $criteria = new CDbCriteria();
        $criteria->addSearchCondition('lower(`t`.last_name)', $term, false);
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

    public function beforeSave()
    {
        if (!$this->correspondence_sign_off_user_id) {
            $this->correspondence_sign_off_user_id = null;
        }

        return parent::beforeSave();
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
     * @return array|CActiveRecord|mixed|Tag|UserAuthentication|null
     */
    public function getAuthenticationForCurrentInstitution()
    {
        $user_auth_id = Yii::app()->db->createCommand()
            ->select('ua.id')
            ->from('institution_authentication ia')
            ->join('user_authentication ua', 'ua.institution_authentication_id = ia.id')
            ->where('ia.institution_id = :institution_id AND ua.user_id = :user_id')
            ->bindValues([':institution_id' => Yii::app()->session['selected_institution_id'], ':user_id' => $this->id])
            ->limit(1)
            ->queryScalar();
        return UserAuthentication::model()->findByPk($user_auth_id);
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
            if ($role == 'admin' && Yii::app()->moduleAPI->get('OETrial')) {
                $trials = Trial::model()->findAll();
                foreach ($trials as $trial) {
                    $newPermission = new UserTrialAssignment();
                    $newPermission->user_id = $this->id;
                    $newPermission->trial_id = $trial->id;
                    $newPermission->trial_permission_id = TrialPermission::model()->find('code = ?', array('MANAGE'))->id;
                    $criteria = new CDbCriteria();
                    $criteria->condition = 'user_id=:user_id AND trial_id=:trial_id AND trial_permission_id=:trial_permission_id';
                    $criteria->params = array(':user_id' => $this->id, ':trial_id' => $trial->id, ':trial_permission_id' => $newPermission->trial_permission_id);
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
            if ($role == 'admin' && Yii::app()->moduleAPI->get('OETrial')) {
                $trials = Trial::model()->findAll();
                foreach ($trials as $trial) {
                    $criteria = new CDbCriteria();
                    $criteria->condition = 'user_id=:user_id AND trial_id=:trial_id AND trial_permission_id=:trial_permission_id AND role IS NULL AND is_principal_investigator=:is_principal_investigator AND is_study_coordinator=:is_study_coordinator';
                    $criteria->params = array(':user_id' => $this->id, ':trial_id' => $trial->id, ':trial_permission_id' => TrialPermission::model()->find('code = ?', array('MANAGE'))->id, ':is_principal_investigator' => 0, ':is_study_coordinator' => 0);
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
        $crit = new CDbCriteria();
        $crit->compare('t.active', 1);
        if (!$this->global_firm_rights) {
            $crit->join =
                'join institution i on i.id = t.institution_id ' .
                'join institution_authentication ia on ia.institution_id = i.id and ia.active = 1 ' .
                'join user_authentication ua ON ua.institution_authentication_id = ia.id and ua.active = 1 ' .
                'left join firm_user_assignment fua on fua.firm_id = t.id and fua.user_id = ua.user_id ' .
                'left join user_firm_rights ufr on ufr.firm_id = t.id and ufr.user_id = ua.user_id ' .
                'left join service_subspecialty_assignment ssa on ssa.id = t.service_subspecialty_assignment_id ' .
                'left join user_service_rights usr on usr.service_id = ssa.service_id and usr.user_id = ua.user_id ';
            $crit->addCondition("fua.id is not null or ufr.id is not null or usr.id is not null");
            $crit->addCondition("ua.user_id = :user_id");
            $crit->params[':user_id'] = $this->id;
        }

        return Firm::model()->findAll($crit);
    }

    public function getAllAvailableFirms()
    {
        $crit = new CDbCriteria();
        $crit->join = "join institution i on i.id = t.institution_id
            join institution_authentication ia on ia.institution_id = i.id and ia.active = 1
            join user_authentication ua on ua.institution_authentication_id = ia.id  and ua.active = 1";
        $crit->condition = 'ua.user_id = ' . Yii::app()->user->id . ' AND t.active = 1';

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
        $criteria = new CDbCriteria();
        $criteria->compare('username', $username);
        $userAuthentication = UserAuthentication::model()->find($criteria);
        return $userAuthentication ? $userAuthentication->user : null;
    }

    /**
     * @return bool
     */
    public function checkSignature()
    {
        return ($this->signature_file_id) ? true : false;
    }

    /**
     * Returns a standalone img tag with a base64-encoded image of the user's signature
     *
     * @param array $html_options   Additional HTML options, @see \CHtml::img()
     * @return string|null  The image or null if the user does not have a saved signature
     */
    public function getSignatureImage(array $html_options = []): ?string
    {
        return !is_null($this->signature_file_id) ?
            \CHtml::image(
                "/protectedFile/view/" . $this->signature_file_id . "/?name=Signature",
                "Signature",
                $html_options
            )
            :
            null;
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

        if ($pw_restrictions === null) {
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
            $pw_restrictions['min_length_message'] = 'Passwords must be at least ' . $pw_restrictions['min_length'] . ' characters long';
        }
        if (!isset($pw_restrictions['max_length'])) {
            $pw_restrictions['max_length'] = 70;
        }
        if (!isset($pw_restrictions['max_length_message'])) {
            $pw_restrictions['max_length_message'] = 'Passwords must be at most ' . $pw_restrictions['max_length'] . ' characters long';
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

        if (!empty($user_ids)) {
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
    // get user permission details
    public function getUserPermissionDetails($tooltip = false)
    {
        $user_roles = Yii::app()->user->getRole($this->id);
        $can_prescribe = in_array('Prescribe', array_values($user_roles));
        $is_med_administer = in_array('Med Administer', array_values($user_roles));
        $ret = array(
            'id' => $this->id,
            'label' => $this->getFullNameAndTitle(),
            'name' => $this->getFullNameAndTitle(),
            'value' => $this->id,
            'grade' => $this->grade ? $this->grade->grade : '',
            'can_prescribe' => $can_prescribe ? 'Yes' : 'No',
            'is_med_administer' => $is_med_administer ? 'Yes' : 'No',
            'consultant' => $this->is_consultant ? 'Yes' : 'No',
        );
        if ($tooltip) {
            $tooltip_str = "";
            $ignore_keys = array('label', 'id', 'name', 'value', 'username');
            foreach ($ret as $key => $val) {
                if (in_array($key, $ignore_keys)) {
                    continue;
                }
                $key = str_replace('_', ' ', $key);
                $key = strtoupper($key);
                $tooltip_str .= "<em>$key: </em>$val<br/>";
            }
            $ret = $tooltip_str;
        }
        return $ret;
    }
    /// NOTE: SSO is not currently supported under the multi-tenancy model. To support it, these functions will likely
    /// need to move to UserAuthentication.
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
        } elseif (Yii::app()->params['auth_source'] === 'OIDC') {
            // Set the user credentials that login through OIDC suthentication
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
            if (!$this->save()) {
                $this->audit('login', 'login-failed', "Cannot create user: $this->username", true);
                throw new Exception('Unable to save User: ' . print_r($this->getErrors(), true));
            }
            $this->id = self::model()->find('username = :username', array(':username' => $this->username))->id;

            $this->setdefaultSSOFirms();
            $this->setdefaultSSORoles();
        } else {
            $this->id = $user->id;
            // Update user information for returning users
            if (Yii::app()->params['auth_source'] === 'OIDC') {
                $this->setIsNewRecord(false);
                $this->update();
            }
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

    /**
     * Get whitelisted actions from password expiry redirects
     * @param string $user
     * @return string Name of status
     */
    public function CheckRequestOnExpiryWhitelist($request)
    {
        $whitelist = !empty(Yii::app()->params['pw_status_checks']['pw_expired_whitelist']) ? Yii::app()->params['pw_status_checks']['pw_expired_whitelist'] : ['/profile/password', '/site/logout', '/User/testAuthenticated', '/Site/loginFromOverlay', 'User/getSecondsUntilSessionExpire', '/site/changesiteandfirm'];

        foreach ($whitelist as $URL) {
            // check to see if the request starts with this whitelisted url
            if (strpos($request, $URL) === 0) {
                return true;
            }
        }
        return false;
    }

    public function getUserNamesWithStatuses()
    {
        $usernames_with_statuses = [];
        foreach ($this->authentications as $authentication) {
            $is_active = $authentication->active ? 'Active' : 'Inactive';
            $password_status = $authentication->isLocalAuth() ? $authentication->password_status : "LDAP";

            $usernames_with_statuses[] = $authentication->username . " ($is_active / $password_status)";
        }

        return $usernames_with_statuses;
    }

    /**
     * Check if provided PIN matches that of User's
     *
     * @param string $pincode
     * @param int|null $user_id
     * @param int|null $institution_id
     * @param int|null $site_id
     * @param UserAuthentication|null $user_authentication will contain a reference to the UserAuthentication if matched
     * @return boolean
     */
    public function checkPin($pincode, $user_id = null, $institution_id = null, $site_id = null, &$user_authentication = null): bool
    {
        $pin_ok = false;

        $institution_id = $institution_id ?? Institution::model()->getCurrent()->id;
        $site_id = $site_id ?? Yii::app()->session['selected_site_id'];
        $user_id = $user_id ?? $this->id;

        $criteria = new CDbCriteria();
        $criteria->with = [
            'user',
            'user.pincode',
            'institutionAuthentication',
        ];
        $criteria->compare('t.user_id', $user_id);
        $criteria->compare('t.active', true);
        $criteria->compare('pincode.pincode', $pincode);
        $criteria->compare('institutionAuthentication.institution_id', $institution_id);
        $criteria->addCondition('site_id=:site_id || site_id IS NULL');
        $criteria->params[':site_id'] = $site_id;

        $user_authentication = UserAuthentication::model()->find($criteria);

        $pin_ok = !is_null($user_authentication);

        return $pin_ok;
    }

    /**
     * @return string pincode value
     */
    public function getPincode()
    {
        return $this->pincode ? $this->pincode->value : 'No Pincode';
    }

    /**
     * generate Pincode for users
     *
     * @param boolean $regenerate indicates if the process is to regenerate pin or to generate new pin
     */
    public function generatePin($regenerate = false)
    {
        $user_pin_obj = $this->pincode ?? new UserPincode();
        if (!$regenerate && !$user_pin_obj->isNewRecord) {
            return;
        }
        $audit_action = $regenerate ? 'Regenerate-pin' : 'Generate-pin';

        $pincode = PincodeHelper::generatePincode();
        $user_pin_obj->user_id = $this->id;
        $user_pin_obj->pincode = $pincode;

        if (!$flag = $user_pin_obj->save()) {
            $this->addErrors($user_pin_obj->getErrors());
        }

        $audit_data = ($flag ? 'Success' : 'Failed') . ": update pincode to $pincode for user {$this->id}";

        $this->audit('pincode', $audit_action, $audit_data);
    }

    /**
     * query pincode history for the last 12 month
     *
     * @return array an array of pincode history
     */
    private function queryPincodeHistory()
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'version_date > NOW() - INTERVAL 12 month';
        return $this->pincode->getPreviousVersionsWithCriteria($criteria);
    }

    /**
     * @return bool indicates if the user reaches the limit
     */
    public function isPincodeRegenReachLimit()
    {
        if (!$this->pincode) {
            return false;
        }
        $results = $this->queryPincodeHistory();

        return self::PIN_REGEN_LIMIT - count($results) === 0;
    }

    /**
     * Shows how many pincode can the user regenerates, and the date resets the count
     *
     * @return string a message to inform user the pincode regenerate status
     */
    public function pincodeRegenStatus()
    {

        $results = $this->queryPincodeHistory();

        $min_date_obj = array_reduce($results, function ($r1, $r2) {
            if (!$r1) {
                return $r2;
            }
            if (!$r2) {
                return $r1;
            }
            return $r1->version_date < $r2->version_date ? $r1 : $r2;
        });
        $remaining = $results ? self::PIN_REGEN_LIMIT - count($results) : self::PIN_REGEN_LIMIT;

        $datetime_format = Helper::NHS_DATE_FORMAT . ' H:i:s';

        $until_date = $min_date_obj ? date($datetime_format, strtotime('+1 year', strtotime($min_date_obj->version_date))) : date($datetime_format, strtotime('+1 year'));

        $msg = "You can regenerate your pincode $remaining time(s) before $until_date";

        return $msg;
    }
}
