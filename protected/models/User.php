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

use OE\factories\models\traits\HasFactory;
use OEModule\OESysEvent\events\UserSavedSystemEvent;
use OEModule\OphCoMessaging\models\Mailbox;
use OEModule\OphCoMessaging\models\MailboxUser;

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
 * @property int $last_firm_id
 *
 * @property User $signOffUser
 * @property Mailbox[] $mailboxes
 * @property Mailbox $personalMailbox
 * @property Team[] $teams
 */
class User extends BaseActiveRecordVersioned
{
    use HasFactory;

    private const PIN_REGEN_LIMIT = 5;
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
                'first_name, last_name, email, global_firm_rights, title, role, is_consultant, is_surgeon,
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
            'teams' => array(self::MANY_MANY, 'Team', 'team_user_assign(user_id, team_id)'),
            'userMailboxes' => [self::HAS_MANY, MailboxUser::class, 'user_id'],
            'mailboxes' => [self::HAS_MANY, Mailbox::class, 'mailbox_id', 'through' => 'userMailboxes'],
            'personalMailbox' => [self::HAS_ONE, Mailbox::class, 'mailbox_id', 'through' => 'userMailboxes', 'on' => 'personalMailbox.is_personal=1']
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

    public function getFormattedRegistrationCode()
    {
        return preg_replace('/^[A-za-z]+[-: ]*/', '', $this->registration_code);
    }

    /**
     * @param $institution_id
     * @return string
     * @throws Exception
     */
    public function getNameAndInstitutionUsername($institution_id, bool $reversed = true, string $username_prefix = '', string $separator = ' '): string
    {
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

        if ($reversed) {
            $return = $this->getReversedFullNameAndTitle($separator);
        } else {
            $return = $this->getFullNameAndTitle($separator);
        }
        return $return . " ({$username_prefix}{$user_auth->username})";
    }

    /**
     * @return string
     */
    public function getFullNameAndTitle(string $separator = ' ')
    {
        return implode($separator, array($this->title, $this->first_name, $this->last_name));
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
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.institution_id = ' . Yii::app()->session['selected_institution_id'] . ' OR t.institution_id IS NULL');
        return $this->getAvailableFirms($criteria);
    }

    public function getInitials()
    {
        return mb_strtoupper($this->first_name[0] ?? null) . mb_strtoupper($this->last_name[0] ?? null);
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
            )) . ($this->contact->qualifications ?? '');
    }

    /**
     * @return string
     */
    public function getReversedFullNameAndTitle(string $separator = ' ')
    {
        return implode($separator, array(strtoupper($this->last_name), ucwords($this->first_name), ucwords($this->title)));
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

    public function afterSave()
    {
        UserSavedSystemEvent::dispatch($this);

        return parent::afterSave();
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

    /**
     * @return CAuthItem[]
     */
    public function getRoles()
    {
        return $this->id ? Yii::app()->authManager->getRoles($this->id) : array();
    }

    public function hasRole($targetRole): bool {
        foreach ($this->getRoles() as $role) {
            if ($targetRole === $role->name) {
                return true;
            }
        }

        return false;
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

        $transaction = Yii::app()->db->getCurrentTransaction() === null ? Yii::app()->db->beginTransaction() : null;
        FirmUserAssignment::model()->deleteAll('user_id = :user_id', array('user_id' => $this->id));
        foreach ($firms as $firm) {
            $firmUserAssign = new FirmUserAssignment();
            $firmUserAssign->user_id = $this->id;
            $firmUserAssign->firm_id = $firm;
            if (!$firmUserAssign->insert()) {
                if ($transaction) {
                    $transaction->rollback();
                }

                throw new CDbException('Unable to save firm assignment');
            }
        }

        if ($transaction) {
            $transaction->commit();
        }
    }

    /**
     * Return all firms that the user has access rights to
     *
     * @return Firm[]
     */
    public function getAvailableFirms(CDbCriteria $crit = null)
    {
        if (!$crit) {
            $crit = new CDbCriteria();
        }
        $crit->compare('t.active', 1);
        if (!$this->global_firm_rights) {
            $crit->join =
                'left join institution i on i.id = t.institution_id ' .
                'left join institution_authentication ia on ia.institution_id = i.id and ia.active = 1 ' .
                'left join user_authentication ua ON ua.institution_authentication_id = ia.id and ua.active = 1 ' .
                'left join firm_user_assignment fua on fua.firm_id = t.id and fua.user_id = :user_id ' .
                'left join user_firm_rights ufr on ufr.firm_id = t.id and ufr.user_id = :user_id ' .
                'left join service_subspecialty_assignment ssa on ssa.id = t.service_subspecialty_assignment_id ' .
                'left join user_service_rights usr on usr.service_id = ssa.service_id and usr.user_id = :user_id ';
            $crit->addCondition("fua.id is not null or ufr.id is not null or usr.id is not null");
            $crit->addCondition("t.institution_id IS NULL OR ua.user_id = :user_id");
            $crit->params[':user_id'] = $this->id;
        }

        return Firm::model()->findAll($crit);
    }

    public function getAllAvailableFirms()
    {
        $crit = new CDbCriteria();
        $crit->join = "left join institution i on i.id = t.institution_id
            left join institution_authentication ia on ia.institution_id = i.id and ia.active = 1
            left join user_authentication ua on ua.institution_authentication_id = ia.id and ua.active = 1";
        $crit->condition = '(t.institution_id IS NULL OR ua.user_id = :user_id) AND t.active = 1';
        $crit->params[':user_id'] = Yii::app()->user->id;

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
        $username = SettingMetadata::model()->getSetting("portal_user") ?: "portal_user";
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

    public function setSAMLSSOUserInformation($response)
    {

        $this->first_name = $response['FirstName'][0];
        $this->last_name = $response['LastName'][0];
        $this->email = $response['username'][0];   // For SAML users, email would be their username
        $this->title = array_key_exists('title', $response) ? $response['title'][0] : '';
        $this->role = array_key_exists('role', $response) ? $response['role'][0] : '';

        $user = self::model()->find('email = :email', array(':email' => $this->email));

        $this->setdefaultSSORights();
        $this->setSSOContact(array_key_exists('qualifications', $response) ? $response['qualifications'][0] : '');

        //If the user is logging into the OE for the first time, assign default roles and firms
        if ($user === null) {
            if (!$this->save()) {
                $this->audit('login', 'login-failed', "Cannot create user with email: $this->email", true);
                throw new Exception('Unable to save User: ' . print_r($this->getErrors(), true));
            }
            $this->id = self::model()->find('email = :email', array(':email' => $this->email))->id;

            $this->setdefaultSSOFirms();
            $this->setdefaultSSORoles();
        } else {
            $this->id = $user->id;
            // Update user information for returning users
            $this->setIsNewRecord(false);
            $this->update();
        }
        // Roles from the token need to be assigned to the user after every login
        $defaultRights = SsoDefaultRights::model()->findByAttributes(['source' => 'SSO']);
        if (!$defaultRights['default_enabled']) {
            // Pass the array of roles from the token
            $roles = isset($response['roles']) ? $response['roles'] : [];
            $this->setRolesFromSSOToken($roles);
        }
    }

    public function setOIDCSSOUserInformation($response)
    {
        $defaultRights = SsoDefaultRights::model()->findByAttributes(['source' => 'SSO']);
        if (!$defaultRights['default_enabled']) {
            $this->checkRolesFromSSOToken($response);
        }

        $allowedKeys = Yii::app()->params['OIDC_settings']['field_mapping_allow_list_with_defaults'];
        foreach ($allowedKeys as $allowedKey => $defaultValue) {
            if (array_key_exists($allowedKey, $response)) {
                $this->$allowedKey = $response[$allowedKey];
            } elseif (!$this->$allowedKey) {
                $this->$allowedKey = $defaultValue;
            }
        }

        $this->setdefaultSSORights();
        $this->setSSOContact(array_key_exists('qualifications', $response) ? $response['qualifications'] : '');

        //If the user is logging into the OE for the first time, assign default roles and firms
        if (!$this->id) {
            if (!$this->save()) {
                $this->audit('login', 'login-failed', "Cannot create user: $this->email", true);
                throw new Exception('Unable to save User: ' . print_r($this->getErrors(), true));
            }

            $this->setdefaultSSOFirms();
            $this->setdefaultSSORoles();
        } else {
            $this->setIsNewRecord(false);
            if (!$this->update()) {
                $this->audit('login', 'login-failed', "Cannot update user: $this->username", true);
                throw new Exception('Unable to save User: ' . print_r($this->getErrors(), true));
            };
        }
        // Roles from the token need to be assigned to the user after every login
        if (!$defaultRights['default_enabled']) {
            // Pass the array of roles from the token
            $roles = isset($response['roles']) ? $response['roles'] : [];
            $this->setRolesFromSSOToken($roles);
        }
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

    // We check that the roles from SSO provider are valid
    public function checkRolesFromSSOToken($response)
    {
        $roles = isset($response['roles']) ? $response['roles'] : [];
        $username = isset($response['email']) ? $response['email'] : '';
        $first_name = isset($response['first_name']) ? $response['first_name'] : '';
        $last_name = isset($response['last_name']) ? $response['last_name'] : '';
        $name = "$first_name $last_name";

        // Deny access if user has no roles
        if (count($roles) < 1) {
            $this->audit('SsoRoles', 'login-failed', "User $name ($username) has no roles assigned: ", true);
            throw new Exception('User has no roles assigned');
        }

        $ssoRoles = SsoRoles::model()->findAll();
        $ssoRoleNames = array_map(function ($role) {
            return $role->name;
        }, $ssoRoles);

        // Deny access if the user doesn't have any of the OpenEyes SSO Roles defined in admin settings
        if (count(array_intersect($roles, $ssoRoleNames)) < 1) {
            $this->audit('SsoRoles', 'login-failed', "User $name ($username) has no valid OpenEyes roles assigned", true);
            throw new Exception('User has no valid OpenEyes roles assigned');
        }

        //Deny access if strict SSO roles check is enabled and user has a role that doesn't exist in OpenEyes SSO roles defined in admin settings
        if (
            Yii::app()->params['strict_SSO_roles_check']
            && count(array_intersect($roles, $ssoRoleNames)) !== count($roles)
        ) {
            $this->audit('SsoRoles', 'login-failed', "User $name ($username) has a role assigned that does not exist in OpenEyes SSO roles", true);
            throw new Exception('User has a role assigned that does not exist in OpenEyes SSO roles');
        }
    }

    public function setRolesFromSSOToken($roles)
    {
        $assignedRoles = array();
        foreach ($roles as $role) {
            $ssoRole = SsoRoles::model()->find("name = :role", [':role' => $role]);
            if (!$ssoRole) {
                continue; // This case covers when user may have a role that doesn't map to any OE SSO roles
            }
            foreach ($ssoRole->sso_roles_assignment as $userRole) {
                if (!in_array($userRole->authitem_role, $assignedRoles, true)) {
                    $assignedRoles[] = $userRole->authitem_role;
                }
            }
        }
        $this->saveRoles($assignedRoles);
    }

    public function setSSOContact(string $qualifications): void
    {
        $contact = $this->contact;
        if (!$contact) {
            $contact = new Contact();
        }

        $contact->title = $this->title;
        $contact->first_name = $this->first_name;
        $contact->last_name = $this->last_name;
        $contact->email = $this->email;
        if ($qualifications) {
            $contact->qualifications = $qualifications;
        }

        if (!$contact->save()) {
            throw new CHttpException(500, 'Unable to save user contact: ' . print_r($contact->getErrors(), true));
        }

        $this->contact_id = $contact->id;
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
            $usernames_with_statuses[] = $authentication->username .
                " <em class='fade'>($is_active / $password_status)</em>" . '<i class="oe-i info small small-icon pro-theme pad fade js-has-tooltip" data-tooltip-content="'
                . ($authentication->institutionAuthentication ? $authentication->institutionAuthentication->description : 'SPEACIAL USER')
                . '"> </i>';
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
        if (isset($this->pincode)) {
            return $this->pincode->getPreviousVersionsWithCriteria($criteria);
        } else {
            return array();
        }
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
