<?php

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
 */
class User extends CActiveRecord
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
		return 'User';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$commonRules = array(
			// Added for uniqueness of username
			array('username', 'unique', 'className' => 'User', 'attributeName' => 'username'),
			array('id, username, first_name, last_name, email, active', 'safe', 'on'=>'search'),
		);

		// @todo - sort out rules for minimum username, first_name etc. length for BASIC, and for LDAP for that matter
		if (Yii::app()->params['auth_source'] == 'BASIC') {
			return array_merge(
				$commonRules,
				array(
					array('username', 'match', 'pattern' => '/^[\w|_]+$/', 'message' => 'Only letters, numbers and underscores are allowed for usernames.'),
					array('username, password, password_repeat, email, first_name, last_name, active', 'required'),
					array('username, password, first_name, last_name', 'length', 'max' => 40),
					array('password', 'length', 'min' => 6, 'message' => 'Passwords must be at least 6 characters long.'),
					array('email', 'length', 'max' => 80),
					array('email', 'email'),
					array('salt', 'length', 'max' => 10),
					// Added for password comparison functionality
					array('password', 'compare'),
					array('password_repeat', 'safe'),
				)
			);
		} else if (Yii::app()->params['auth_source'] == 'LDAP') {
			return array_merge(
				$commonRules,
				array(
					array('username, active', 'required'),
					array('username', 'length', 'max' => 40)
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
		);
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
	public function save()
	{
		if (Yii::app()->params['auth_source'] == 'BASIC') {
			/**
			 * AUTH_BASIC requires creation of a salt. AUTH_LDAP doesn't.
			 */
			if ($this->getIsNewRecord()) {
				$salt = '';
				$possible = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

				for($i=0; $i < 10; $i++) {
					$salt .= $possible[mt_rand(0, strlen($possible)-1)];
				}

				$this->salt = $salt;
			}
		}

		return parent::save();
	}

	/**
	 * Hashes the user password for insertion into the db.
	 */
	protected function afterValidate()
	{
		parent::afterValidate();
		$this->password = $this->hashPassword($this->password, $this->salt);
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
}