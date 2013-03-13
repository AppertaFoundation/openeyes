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
		if (!in_array(Yii::app()->params['ldap_method'],array('native','zend'))) {
			throw new Exception('Unsupported LDAP authentication method: '.Yii::app()->params['ldap_method'].', please use native or zend.');
		}

		Yii::app()->event->dispatch('user_before_login', array('username' => $this->username));

		/**
		 * Usernames are case sensitive
		 */
		$user = User::model()->find('username = ?', array($this->username));
		if($user === null) {
			Audit::add('login','login-failed',"User not found in local database: $this->username",true);
			$this->errorCode = self::ERROR_USERNAME_INVALID;
			return false;
		} else if($user->active != 1) {
			$user->audit('login','login-failed',"User not active and so cannot login: $this->username",true);
			$this->errorCode = self::ERROR_USER_INACTIVE;
			return false;
		}

		if (in_array($user->username,Yii::app()->params['local_users'])) {
			Yii::app()->params['auth_source'] = 'BASIC';
		}

		$this->password = utf8_decode($this->password);

		/**
		 * Here we diverge depending on the authentication source.
		 */
		if (Yii::app()->params['auth_source'] == 'LDAP') {
			/**
			 * Required for LDAP authentication
			 */
			if (Yii::app()->params['ldap_method'] == 'zend') {
				Yii::import('application.vendors.*');
				require_once('Zend/Ldap.php');

				/**
				 * Check with LDAP for authentication
				 */
				$options = array(
					'host'				=> Yii::app()->params['ldap_server'],
					'port'				=> Yii::app()->params['ldap_port'],
					'username'			=> Yii::app()->params['ldap_admin_dn'],
					'password'			=> Yii::app()->params['ldap_password'],
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
					 * User not authenticated via LDAP
					 */
					$audit = new Audit;
					$audit->action = "login-failed";
					$audit->target_type = "login";
					$audit->user_id = $user->id;
					$audit->data = "Login failed for user {$this->username}: LDAP authentication failed: ".$e->getMessage().": ".$this->username;
					$audit->save();
					OELog::log("Login failed for user {$this->username}: LDAP authentication failed: ".$e->getMessage(),$this->username);

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

			} else {
				if (!$link = ldap_connect(Yii::app()->params['ldap_server'])) {
					throw new Exception('Unable to connect to LDAP server.');
				}

				ldap_set_option($link, LDAP_OPT_NETWORK_TIMEOUT, Yii::app()->params['ldap_native_timeout']);

				if (!@ldap_bind($link, "cn=$this->username,".Yii::app()->params['ldap_dn'], $this->password)) {
					$audit = new Audit;
					$audit->action = "login-failed";
					$audit->target_type = "login";
					$audit->user_id = $user->id;
					$audit->data = "Login failed for user {$this->username}: LDAP authentication failed: ".ldap_error($link);
					$audit->save();
					OELog::log("Login failed for user {$this->username}: LDAP authentication failed: ".ldap_error($link));

					$this->errorCode = self::ERROR_USERNAME_INVALID;
					return false;
				}

				$sr = ldap_search($link, "cn=$this->username,".Yii::app()->params['ldap_dn'], "cn=$this->username");
				$info = ldap_get_entries($link, $sr);
				if (!isset($info[0])) {
					throw new Exception("Failed to retrieve ldap info for user $user->username: ".ldap_error($link)." [".print_r($info,true)."]");
				}
				$info = $info[0];
			}

			/**
			 * Update user db record with details from LDAP.
			 */
			if (isset($info['givenname'][0])) {
				$user->first_name = $info['givenname'][0];
			}
			if (isset($info['sn'][0])) {
				$user->last_name = $info['sn'][0];
			}
			if (isset($info['mail'][0])) {
				$user->email = $info['mail'][0];
			}
			if (!$user->save()) {
				$user->audit('login','login-failed',"Login failed for user {$this->username}: unable to update user with details from LDAP: ".print_r($user->getErrors(),true),true);
				throw new SystemException('Unable to update user with details from LDAP: '.print_r($user->getErrors(),true));
			}
		} else if (Yii::app()->params['auth_source'] == 'BASIC') {
			if(!$user->validatePassword($this->password)) {
				$this->errorCode = self::ERROR_PASSWORD_INVALID;
				$user->audit('login','login-failed',"Login failed for user {$this->username}: invalid password",true);
				return false;
			}
		} else {
			/**
			 * Unknown auth_source, error
			 */
			$user->audit('login','login-failed',"Login failed for user {$this->username}: unknown auth source: " . Yii::app()->params['auth_source'],true);
			throw new SystemException('Unknown auth_source: ' . Yii::app()->params['auth_source']);
		}

		$this->_id = $user->id;
		$this->username = $user->username;
		$this->errorCode = self::ERROR_NONE;

		// Get all the user's firms and put them in a session
		$app = Yii::app();

		$firms = array();

		if ($user->global_firm_rights) {
			foreach(Firm::model()->findAll() as $firm) {
				$firms[$firm->id] = $this->firmString($firm);
			}
		} else {
			// Gets the firms the user is associated with
			foreach ($user->firms as $firm) {
				$firms[$firm->id] = $this->firmString($firm);
			}

			// Get arbitrarily selected firms
			foreach ($user->firmRights as $firm) {
				$firms[$firm->id] = $this->firmString($firm);
			}

			// Get firms associated with services
			foreach ($user->serviceRights as $service) {
				foreach ($service->serviceSubspecialtyAssignments as $ssa) {
					foreach (Firm::model()->findAll(
						'service_subspecialty_assignment_id = ?', array(
							$ssa->id
						)
					) as $firm) {
						$firms[$firm->id] = $this->firmString($firm);
					}
				}
			}
		}

		if (!count($firms)) {
			$user->audit('login','login-failed',"Login failed for user {$this->username}: user has no firm rights and cannot use the system",true);
			throw new Exception('User has no firm rights and cannot use the system.');
		}

		natcasesort($firms);

		$app->session['user'] = $user;
		$app->session['firms'] = $firms;

		reset($firms);

		if ($user->last_firm_id) {
			$app->session['selected_firm_id'] = $user->last_firm_id;
		} else if (count($user->firms)) {
			// Set the firm to one the user is associated with
			$userFirms = $user->firms;
			$app->session['selected_firm_id'] = $userFirms[0]->id;
		} else {
			// The user doesn't have firms of their own to select from so we select
			//	one arbitrarily
			$app->session['selected_firm_id'] = key($firms);
		}

		if ($site = Site::model()->findByPk(@$_POST['LoginForm']['siteId'])) {
			$app->session['selected_site_id'] = $site->id;
		}

		$user->audit('login','login-successful',"User ".strtoupper($this->username)." logged in",true);

		return true;
	}

	public function firmString($firm)
	{
		return "{$firm->name} ({$firm->serviceSubspecialtyAssignment->subspecialty->name})";
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
