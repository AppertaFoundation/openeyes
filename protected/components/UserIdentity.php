<?php
/**
 * OpenEyes.
 *
 * 
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
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
     * @return bool whether authentication succeeds.
     *
     * @throws
     */
    public function authenticate($force = false)
    {
        if (!in_array(Yii::app()->params['ldap_method'], array('native', 'zend', 'native-search'))) {
            throw new Exception('Unsupported LDAP authentication method: '.Yii::app()->params['ldap_method'].', please use native or zend.');
        }

        Yii::app()->event->dispatch('user_before_login', array('username' => $this->username));

        /*
         * Usernames are case sensitive
         */
        $user = User::model()->find('username = :username', array('username' => $this->username));
        if ($user === null) {
            Audit::add('login', 'login-failed', null, "User not found in local database: $this->username");
            $this->errorCode = self::ERROR_USERNAME_INVALID;

            return false;
        } elseif (!$force && $user->active != 1) {
            $user->audit('login', 'login-failed', null, "User not active and so cannot login: $this->username");
            $this->errorCode = self::ERROR_USER_INACTIVE;

            return false;
        } elseif (!$force && !Yii::app()->getAuthManager()->checkAccess('OprnLogin', $user->id)) {
            $user->audit('login', 'login-failed', "User has not been assigned OprnLogin and so cannot login: $this->username", true);
            $this->errorCode = self::ERROR_USER_INACTIVE;

            return false;
        }

        if (in_array($user->username, Yii::app()->params['local_users'])) {
            Yii::app()->params['auth_source'] = 'BASIC';
        }

        if (Yii::app()->params['utf8_decode_required']){
            $this->password = utf8_decode($this->password);
        }

        /*
         * Here we diverge depending on the authentication source.
         */
        if (Yii::app()->params['auth_source'] === 'LDAP') {
            /*
             * Required for LDAP authentication
             */
            if (Yii::app()->params['ldap_method'] == 'zend') {
                Yii::import('application.vendors.*');
                require_once 'Zend/Ldap.php';

                /*
                 * Check with LDAP for authentication
                 */
                $options = array(
                    'host' => Yii::app()->params['ldap_server'],
                    'port' => Yii::app()->params['ldap_port'],
                    'username' => Yii::app()->params['ldap_admin_dn'],
                    'password' => Yii::app()->params['ldap_password'],
                    'baseDn' => Yii::app()->params['ldap_admin_dn'],
                    'useStartTls' => false,
                );

                $ldap = $this->getLdap($options);

                /*
                 * Try and bind to the login details provided. This indicates if
                 * the user is in LDAP.
                 */

                try {
                    $ldap->bind(
                        'cn='.$this->username.','.Yii::app()->params['ldap_dn'],
                        $this->password
                    );
                } catch (Exception $e) {
                    /*
                     * User not authenticated via LDAP
                     */
                    $audit = new Audit();
                    $audit->action = 'login-failed';
                    $audit->target_type = 'login';
                    $audit->user_id = $user->id;
                    $audit->data = "Login failed for user {$this->username}: LDAP authentication failed: ".$e->getMessage().': '.$this->username;
                    $audit->save();
                    OELog::log("Login failed for user {$this->username}: LDAP authentication failed: ".$e->getMessage(), $this->username);

                    $this->errorCode = self::ERROR_USERNAME_INVALID;

                    return false;
                }

                /*
                 * User is in LDAP, get their details.
                 */
                $info = $ldap->getEntry(
                    'cn='.$this->username.','.Yii::app()->params['ldap_dn'],
                    array('givenname', 'sn', 'mail')
                );
            } elseif (Yii::app()->params['ldap_method'] == 'native-search') {
                if (preg_match('~ldaps?://~', Yii::app()->params['ldap_server'])) {
                    if (!$link = ldap_connect(Yii::app()->params['ldap_server'])) {
                        throw new Exception('Unable to connect to LDAP server: '.Yii::app()->params['ldap_server']);
                    }
                } else {
                    if (!$link = ldap_connect(Yii::app()->params['ldap_server'], Yii::app()->params['ldap_port'])) {
                        throw new Exception('Unable to connect to LDAP server: '.Yii::app()->params['ldap_server'].' '.Yii::app()->params['ldap_port']);
                    }
                }

                ldap_set_option($link, LDAP_OPT_REFERRALS, 0);
                if (Yii::app()->params['ldap_protocol_version'] !== null) {
                    ldap_set_option($link, LDAP_OPT_PROTOCOL_VERSION, Yii::app()->params['ldap_protocol_version']);
                }
                ldap_set_option($link, LDAP_OPT_NETWORK_TIMEOUT, Yii::app()->params['ldap_native_timeout']);

                // Bind as the LDAP admin user. Set parameters ldap_admin_dn and ldap_password in local config for this.
                if (!@ldap_bind($link, Yii::app()->params['ldap_admin_dn'], Yii::app()->params['ldap_password'])) {
                    $audit = new Audit();
                    $audit->action = 'login-failed';
                    $audit->target_type = 'login';
                    $audit->user_id = $user->id;
                    $audit->data = "Login failed for user {$this->username}: LDAP admin bind failed: ".ldap_error($link);
                    $audit->save();
                    OELog::log("Login failed for user {$this->username}: LDAP admin bind failed: ".ldap_error($link));

                    $this->errorCode = self::ERROR_USERNAME_INVALID;

                    return false;
                }

                // Perform an LDAP search for the username in order to retrieve their DN. Set the base DN in parameter ldap_dn in local config for this.

                $ldapSearchFilter = '(sAMAccountName='.$this->username.')';

                $ldapSearchResult = ldap_search($link, Yii::app()->params['ldap_dn'], $ldapSearchFilter);

                $ldapSearchEntries = ldap_get_entries($link, $ldapSearchResult);

                if ($ldapSearchEntries['count'] != 1) {
                    $audit = new Audit();
                    $audit->action = 'login-failed';
                    $audit->target_type = 'login';
                    $audit->user_id = $user->id;
                    $audit->data = "Login failed for user {$this->username}: LDAP search did not return exactly 1 result: ".$ldapSearchEntries['count'];
                    $audit->save();
                    OELog::log("Login failed for user {$this->username}: LDAP search did not return exactly 1 result: ".$ldapSearchEntries['count']);

                    $this->errorCode = self::ERROR_USERNAME_INVALID;

                    return false;
                }

                $info = $ldapSearchEntries[0];

                // Now attempt to bind to the user's DN with their entered password.

                if (!@ldap_bind($link, $info['distinguishedname'][0], $this->password)) {
                    $audit = new Audit();
                    $audit->action = 'login-failed';
                    $audit->target_type = 'login';
                    $audit->user_id = $user->id;
                    $audit->data = "Login failed for user {$this->username}: LDAP authentication failed: ".ldap_error($link);
                    $audit->save();
                    OELog::log("Login failed for user {$this->username}: LDAP authentication failed: ".ldap_error($link));

                    $this->errorCode = self::ERROR_USERNAME_INVALID;

                    return false;
                }
            } else {
                // verify we can reach the server, as ldap_connect doesn't timeout correctly
                if ($fp = @fsockopen(Yii::app()->params['ldap_server'], 389, $errno, $errstr, 5)) {
                    if (!$link = ldap_connect(Yii::app()->params['ldap_server'])) {
                        throw new Exception('Unable to connect to LDAP server.');
                    }
                } else {
                    throw new Exception('Unable to reach ldap server: '.Yii::app()->params['ldap_server'].': '.$errstr);
                }

                ldap_set_option($link, LDAP_OPT_NETWORK_TIMEOUT, Yii::app()->params['ldap_native_timeout']);
                if (Yii::app()->params['ldap_protocol_version'] !== null) {
                    ldap_set_option($link, LDAP_OPT_PROTOCOL_VERSION, Yii::app()->params['ldap_protocol_version']);
                }
                $ldap_user_prefix = Yii::app()->params['ldap_username_prefix'] ?: 'cn';

                if (!@ldap_bind($link, "$ldap_user_prefix=$this->username,".Yii::app()->params['ldap_dn'], $this->password)) {
                    $audit = new Audit();
                    $audit->action = 'login-failed';
                    $audit->target_type = 'login';
                    $audit->user_id = $user->id;
                    $audit->data = "Login failed for user {$this->username}: LDAP authentication failed: ".ldap_error($link);
                    $audit->save();
                    OELog::log("Login failed for user {$this->username}: LDAP authentication failed: ".ldap_error($link));

                    $this->errorCode = self::ERROR_USERNAME_INVALID;

                    return false;
                }

                $attempts = isset(Yii::app()->params['ldap_info_retries']) ? Yii::app()->params['ldap_info_retries'] : 1;

                for ($i = 0; $i < $attempts; ++$i) {
                    if ($i > 0 && isset(Yii::app()->params['ldap_info_retry_delay'])) {
                        sleep(Yii::app()->params['ldap_info_retry_delay']);
                    }
                    $sr = ldap_search($link, "$ldap_user_prefix=$this->username,".Yii::app()->params['ldap_dn'], "uid=$this->username");
                    $info = ldap_get_entries($link, $sr);

                    if (isset($info[0])) {
                        break;
                    }
                }

                if (!isset($info[0])) {
                    throw new Exception("Failed to retrieve ldap info for user $user->username: ".ldap_error($link).' ['.print_r($info, true).']');
                }
                $info = $info[0];
            }

            /*
             * Update user db record with details from LDAP.
             */
            if (Yii::app()->params['ldap_update_name']) {
                if (isset($info['givenname'][0])) {
                    $user->first_name = $info['givenname'][0];
                }
                if (isset($info['sn'][0])) {
                    $user->last_name = $info['sn'][0];
                }
            }
            if (Yii::app()->params['ldap_update_email']) {
                if (isset($info['mail'][0])) {
                    $user->email = $info['mail'][0];
                }
            }

            // using isModelDirty() because
            // $user->saveOnlyIfDirty()->save() returns false if the model wasn't dirty => model wasn't saved
            if ($user->isModelDirty()) {
                $user->password_hashed = true;
                if (!$user->save()) {
                    $user->audit('login', 'login-failed', null, "Login failed for user {$this->username}: unable to update user with details from LDAP: ".print_r($user->getErrors(), true));
                    throw new SystemException('Unable to update user with details from LDAP: '.print_r($user->getErrors(), true));
                }
            }
        } elseif (Yii::app()->params['auth_source'] === 'BASIC') {
            $user->userLogOnAttemptsCheck($user);
            $validPw=$user->validatePassword($this->password);
            $pwActive = !$user->testUserPWStatus('locked');
            if (!$force && !($validPw && $pwActive)) { //if failed logon or locked
            $user->userLogOnAttemptsCheck($user);
                if(!$validPw && !empty(Yii::app()->params['pw_status_checks']['pw_tries'])){ // if the password was not correct and we check the number of tries
                    $user->setFailedLogin();                    
                    $user->userLogOnAttemptsCheck($user);
                }
                $this->errorCode = self::ERROR_PASSWORD_INVALID;
                $user->audit('login', 'login-failed', null, "Login failed for user {$this->username}: ". ($validPw ?'valid password':'invalid password'));

                return false;
            }
        } elseif (Yii::app()->params['auth_source'] === 'SAML' || Yii::app()->params['auth_source'] === 'OIDC') {
            // The user is already authenticated from the portal so directly register the username for the session
            $user->username = $this->username;
        } else {
            /*
             * Unknown auth_source, error
             */
            $user->audit('login', 'login-failed', null, "Login failed for user {$this->username}: unknown auth source: ".Yii::app()->params['auth_source']);
            throw new SystemException('Unknown auth_source: '.Yii::app()->params['auth_source']);
        }

        $this->_id = $user->id;
        $this->username = $user->username;
        $this->errorCode = self::ERROR_NONE;
        // Get all the user's firms and put them in a session
        $app = Yii::app();

        $firms = array();

        foreach ($user->getAvailableFirms() as $firm) {
            $firms[$firm->id] = $this->firmString($firm);
        }

        if (!count($firms)) {
            $user->audit('login', 'login-failed', null, "Login failed for user {$this->username}: user has no firm rights and cannot use the system");
            throw new Exception('User has no firm rights and cannot use the system.');
        }

        natcasesort($firms);

        $app->session['user'] = $user;
        $app->session['firms'] = $firms;

        reset($firms);

        // Select firm
        if ($user->last_firm_id) {
            $app->session['selected_firm_id'] = $user->last_firm_id;
        } elseif (count($user->firms)) {
            // Set the firm to one the user is associated with
            $userFirms = $user->firms;
            $app->session['selected_firm_id'] = $userFirms[0]->id;
        } else {
            // The user doesn't have firms of their own to select from so we select
            // one arbitrarily
            $app->session['selected_firm_id'] = key($firms);
        }

        // Select site
        if ($user->last_site_id) {
            $app->session['selected_site_id'] = $user->last_site_id;
        } elseif ($default_site = Site::model()->getDefaultSite()) {
            $app->session['selected_site_id'] = $default_site->id;
        } else {
            throw new CException('Cannot find default site');
        }

        $user->audit('login', 'login-successful', null, 'User '.strtoupper($this->username).' logged in');

        return true;
    }

    public function firmString($firm)
    {
        if ($firm->serviceSubspecialtyAssignment) {
            return "{$firm->name} ({$firm->serviceSubspecialtyAssignment->subspecialty->name})";
        }

        return $firm->name;
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
