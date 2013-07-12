<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is the model class for table "User".
 *
 * The followings are the available columns in table 'User':
 * @property integer $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property integer $active
 * @property string $password
 * @property string $salt
 * @property integer $global_firm_rights
 */
class User extends BaseActiveRecord
{
	/**
	 * Used to check password and password confirmation match
	 * @var string
	 */
	public $password_repeat;

	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
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
			array('id, username, first_name, last_name, email, active, global_firm_rights', 'safe', 'on'=>'search'),
			array('username, first_name, last_name, email, active, global_firm_rights, is_doctor, title, qualifications, role, salt, access_level, password, is_clinical, is_consultant, is_surgeon, has_selected_firms', 'safe'),
		);

		if (Yii::app()->params['auth_source'] == 'BASIC') {
			return array_merge(
				$commonRules,
				array(
					array('username', 'match', 'pattern' => '/^[\w|_]+$/', 'message' => 'Only letters, numbers and underscores are allowed for usernames.'),
					array('username, email, first_name, last_name, active, global_firm_rights', 'required'),
					array('username, password, first_name, last_name', 'length', 'max' => 40),
					array('password', 'length', 'min' => 5, 'message' => 'Passwords must be at least 6 characters long.'),
					array('email', 'length', 'max' => 80),
					array('email', 'email'),
					array('salt', 'length', 'max' => 10),
					// Added for password comparison functionality
					array('password_repeat', 'safe'),
				)
			);
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
			'firms' => array(self::MANY_MANY, 'Firm', 'firm_user_assignment(firm_id, user_id)'),
			'firmRights' => array(self::MANY_MANY, 'Firm', 'user_firm_rights(firm_id, user_id)'),
			'serviceRights' => array(self::MANY_MANY, 'Service', 'user_service_rights(service_id, user_id)'),
			'contact' => array(self::BELONGS_TO, 'Contact', 'contact_id'),
			'firm_preferences' => array(self::HAS_MANY, 'UserFirmPreference', 'user_id'),
			'preferred_firms' => array(self::HAS_MANY, 'Firm', 'firm_id', 'through' => 'firm_preferences', 'order' => 'firm_preferences.position DESC', 'limit' => 5),
			'firmSelections' => array(self::MANY_MANY, 'Firm', 'user_firm(firm_id, user_id)', 'order' => 'name asc'),
			'siteSelections' => array(self::MANY_MANY, 'Site', 'user_site(site_id, user_id)', 'order' => 'name asc'),
		);
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
			'is_doctor' => 'Doctor',
			'is_consultant' => 'Consultant',
			'is_clinical' => 'Clinically trained',
			'is_surgeon' => 'Surgeon',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('active',$this->active);
		$criteria->compare('global_firm_rights',$this->global_firm_rights);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Saves or updates a db record and creates the salt for a new record of
	 *	authentication type 'basic'.
	 *
	 * @return boolean
	 */
	public function save($runValidation = true, $attributes = null, $allow_overriding=false)
	{
		if (Yii::app()->params['auth_source'] == 'BASIC') {
			/**
			 * AUTH_BASIC requires creation of a salt. AUTH_LDAP doesn't.
			 */
			if ($this->getIsNewRecord() && !$this->salt) {
				$salt = '';
				$possible = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

				for ($i=0; $i < 10; $i++) {
					$salt .= $possible[mt_rand(0, strlen($possible)-1)];
				}

				$this->salt = $salt;
			}
		}

		return parent::save($runValidation, $attributes, $allow_overriding);
	}

	/**
	 * Hashes the user password for insertion into the db.
	 */
	protected function afterValidate()
	{
		parent::afterValidate();

		if (!preg_match('/^[0-9a-f]{32}$/',$this->password)) {
			$this->password = $this->hashPassword($this->password, $this->salt);
		}
	}

	/**
	 * Returns an md5 hash of the password and username provided.
	 *
	 * @param string $password
	 * @param string $salt
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
	 * @return boolean
	 */
	public function validatePassword($password)
	{
		return $this->hashPassword($password, $this->salt) === $this->password;
	}

	/**
	 * Displays a string indicating whether the user account is active
	 * @return String
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
	 * Displays a string indicating whether the user account has global firm rights
	 *
	 * @return String
	 */
	public function getGlobalFirmRightsText()
	{
		if ($this->global_firm_rights) {
			return 'Yes';
		} else {
			return 'No';
		}
	}

	public function getFullName()
	{
		return implode(' ', array($this->first_name, $this->last_name));
	}

	public function getReversedFullName()
	{
		return implode(' ', array($this->last_name, $this->first_name));
	}

	public function getFullNameAndTitle()
	{
		return implode(' ', array($this->title, $this->first_name, $this->last_name));
	}

	public function getFullNameAndTitleAndQualifications()
	{
		return implode(' ', array($this->title, $this->first_name, $this->last_name)).($this->qualifications?' '.$this->qualifications:'');
	}

	public function getReversedFullNameAndTitle()
	{
		return implode(' ', array($this->title, $this->last_name, $this->first_name));
	}

	/**
	 * Returns whether this user has a contact entry and a consultant entry
	 *			i.e. they are a consultant for the centre.
	 *
	 * @return boolean
	 */
	public static function isConsultant()
	{
		$user = User::model()->findByPk(Yii::app()->id);

		// Set whether they are an internal consultant or not. This gives them the ability to edit macros.
		if (isset($user->contact->consultant)) {
			return true;
		}

		return false;
	}

	public function getList()
	{
		$users = array();

		foreach (User::Model()->findAll(array('order'=>'first_name,last_name')) as $user) {
			$users[$user->id] = $user->first_name.' '.$user->last_name;
		}

		return $users;
	}

	/**
	 * Returns the users that are eligible to be considered surgeons.
	 *
	 * @return User[] List of surgeon users
	 */
	public static function getSurgeons()
	{
		$criteria = new CDbCriteria;
		$criteria->compare('is_surgeon',1);
		$criteria->compare('active',1);
		$criteria->order = 'last_name,first_name asc';

		return User::model()->findAll($criteria);
	}

	public function audit($target, $action, $data=null, $log=false, $properties=array())
	{
		$properties['user_id'] = $this->id;
		return parent::audit($target, $action, $data, $log, $properties);
	}

	public function getListSurgeons()
	{
		$criteria = new CDbCriteria;
		$criteria->compare('is_doctor',1);
		$criteria->compare('active',1);
		$criteria->order = 'last_name,first_name asc';
		return CHtml::listData(User::model()->findAll($criteria),'id','reversedFullName');
	}

	public function getReportDisplay()
	{
		return $this->fullName;
	}

	public function beforeValidate()
	{
		if (!preg_match('/^[0-9a-f]{32}$/',$this->password)) {
			if ($this->password != $this->password_repeat) {
				$this->addError('password','Password confirmation must match exactly');
			}
			$this->salt = $this->randomSalt();
		}

		if ($this->getIsNewRecord() && !$this->password) {
			$this->addError('password','Password is required');
		}

		return parent::beforeValidate();
	}

	public function randomSalt()
	{
		$salt = '';
		for ($i=0;$i<10;$i++) {
			switch (rand(0,2)) {
				case 0:
					$salt .= chr(rand(48,57));
					break;
				case 1:
					$salt .= chr(rand(65,90));
					break;
				case 2:
					$salt .= chr(rand(97,122));
					break;
			}
		}

		return $salt;
	}

	public function getAccessLevelOptions()
	{
		return array(
			0 => 'No access',
			1 => 'Patient demographics',
			2 => 'Read only',
			3 => 'Read only and print',
			4 => 'Edit but not prescribe',
			5 => 'Full',
		);
	}

	public function getAccesslevelstring()
	{
		$access_levels = $this->getAccessLevelOptions();
		if (isset($access_levels[$this->access_level])) {
			return $access_levels[$this->access_level];
		}
	}

	public function findAsContacts($term)
	{
		$contacts = array();

		$criteria = new CDbCriteria;
		$criteria->addSearchCondition("lower(`t`.last_name)",$term,false);
		$criteria->compare('active',1);
		$criteria->order = 'contact.title, contact.first_name, contact.last_name';

		foreach (User::model()->with(array('contact' => array('with' => 'locations')))->findAll($criteria) as $user) {
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
		if (empty(Yii::app()->params['institution_code'])) {
			throw new Exception("Institution code is not set");
		}

		if (!$institution = Institution::model()->find('remote_id=?',array(Yii::app()->params['institution_code']))) {
			throw new Exception("Institution not found: ".Yii::app()->params['institution_code']);
		}

		$site_ids = array();
		foreach ($this->siteSelections as $site) {
			$site_ids[] = $site->id;
		}

		$criteria = new CDbCriteria;
		$criteria->addCondition('institution_id=:institution_id');
		$criteria->addNotInCondition('id',$site_ids);
		$criteria->params[':institution_id'] = $institution->id;
		$criteria->order = 'name asc';

		return Site::model()->findAll($criteria);
	}

	public function getNotSelectedFirmList()
	{
		$firms = Yii::app()->db->createCommand()
			->select('f.id, f.name, s.name AS subspecialty')
			->from('firm f')
			->leftJoin('service_subspecialty_assignment ssa', 'f.service_subspecialty_assignment_id = ssa.id')
			->leftJoin('subspecialty s','ssa.subspecialty_id = s.id')
			->leftJoin('user_firm uf','uf.firm_id = f.id and uf.user_id = '.Yii::app()->user->id)
			->where("uf.id is null",array(':userId'=>Yii::app()->user->id))
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
}
