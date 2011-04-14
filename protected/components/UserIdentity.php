<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */

class UserIdentity extends CUserIdentity
{
	private $_id;

	/*
	 * New error code for users with active set to 0
	 */
	const ERROR_USER_INACTIVE = 3;

	/**
	 * Authenticates a user.
	 *
	 * Uses either BASIC or LDAP authentication. BASIC authenticates against
	 * the openeyes DB. LDAP uses whichever LDAP is specified in the params.php
	 * config file.
	 *
	 * @return boolean whether authentication succeeds.
	 * @throws
	 */
	public function authenticate()
	{
		/**
		 * Usernames are case sensitive
		 */
		$user = User::model()->find('username = ?', array($this->username));
		if($user === null) {
			$this->errorCode = self::ERROR_USERNAME_INVALID;
			return false;
		} else if($user->active != 1) {
			$this->errorCode = self::ERROR_USER_INACTIVE;
			return false;
		}

		/**
		 * Here we diverge depending on the authentication source.
		 */
		if (Yii::app()->params['auth_source'] == 'LDAP') {
			/**
			 * Required for LDAP authentication
			 */
			Yii::import('application.vendors.*');
			require_once('Zend/Ldap.php');

			/**
			 * Check with LDAP for authentication
			 */
			$options = array(
				'host'				=> Yii::app()->params['ldap_server'],
				'port'				=> Yii::app()->params['ldap_port'],
				'username'			=>	Yii::app()->params['ldap_admin_dn'],
				'password'			=>	Yii::app()->params['ldap_password'],
				'baseDn'			=> Yii::app()->params['ldap_admin_dn'],
				'useStartTls'		=> false,
			);

		    $ldap = $this->getLdap($options);

			/**
			 * Try and bind to the login details provided. This indicates if
			 * the user is in LDAP.
			 */
			try {
				$ldap->bind(
					"cn=" . $this->username . "," . Yii::app()->params['ldap_dn'],
					$this->password
				);
			} catch (Exception $e){
				/**
				 * User isn't in LDAP.
				 */
				$this->errorCode = self::ERROR_USERNAME_INVALID;
				return false;
			}

			/**
			 * User is in LDAP, get their details.
			 */
			$info = $ldap->getEntry(
				"cn=" . $this->username . "," . Yii::app()->params['ldap_dn'],
				array('givenname', 'sn', 'mail')
			);

			/**
			 * Update user db record with details from LDAP.
			 */
			$user->first_name = $info['givenname'][0];
			$user->last_name = $info['sn'][0];
			$user->email = $info['mail'][0];
			$user->save();
		} else if (Yii::app()->params['auth_source'] == 'BASIC') {
			if(!$user->validatePassword($this->password)) {
				$this->errorCode = self::ERROR_PASSWORD_INVALID;
				return false;
			}
		} else {
			/**
			 * Unknown auth_source, error
			 */
			 throw new SystemException('Unknown auth_source: ' . Yii::app()->params['auth_source']);
		}

		$this->_id = $user->id;
		$this->username = $user->username;
		$this->errorCode = self::ERROR_NONE;

		// Get all the user's firms and put them in a session
		$app = Yii::app();

		$firms = array();

		foreach ($user->firms as $firm) {
			$firms[$firm->id] = $firm->name .
								' (' . $firm->pas_code . ') (' .
								$firm->serviceSpecialtyAssignment->service->name .')';

			// @todo - decide how to select the default firm
			$app->session['selected_firm_id'] = $firm->id;
		}

		$app->session['firms'] = $firms;

		return true;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function getLdap($options)
	{
		return new Zend_Ldap($options);
	}
}