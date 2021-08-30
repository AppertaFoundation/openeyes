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
    private $institution_id;
    private $site_id;
    private $available_authentications;
    private $available_auth_error;
    private $is_special = false;
    private $user_id;

    /*
     * New error code for users with active set to 0
     */
    const ERROR_USER_INACTIVE = 3;

    /**
     * Constructor.
     * @param string $username username
     * @param string $password password
     * @param int $institution_id institution id
     * @param int $site_id site id
     */
    public function __construct($username,$password,$institution_id = null,$site_id = null,$user_pin = null)
    {
            $this->institution_id = $institution_id;
            $this->site_id = $site_id;
            $available_auth_result = UserAuthentication::findAvailableAuthentications($username, $institution_id, $site_id);
            $this->available_authentications = $available_auth_result[0];
            $this->available_auth_error = $available_auth_result[1];
        if (is_null($user_pin)) {
            parent::__construct($username, $password);
        } else {
            $this->username = $username;
        }
    }

    public function authenticate()
    {
        Yii::app()->event->dispatch('user_before_login', array('username' => $this->username));


        if (empty($this->available_authentications)) {
            Audit::add('login', 'login-failed', null, "User with correct permissions not found: $this->username, Specific error: $this->available_auth_error");
            $this->errorCode = self::ERROR_USERNAME_INVALID;

            return [false, $this->available_auth_error];
        }

        foreach([InstitutionAuthentication::EXACT_MATCH, InstitutionAuthentication::PERMISSIVE_MATCH] as $match_type) {
            $result = $this->authenticateUserAuthenticationType($match_type);
            if (isset($result)) {
                return $result;
            }
        }

        return [false, "Invalid login."];
    }

    private function authenticateUserAuthenticationType($type)
    {
        if (isset($this->available_authentications[$type])) {
            if (count($this->available_authentications[$type]) > 1) {
                Audit::add('login', 'login-failed', null, "User has multiple UserAuthentications of type $type for, Site: $this->site_id, Institution: $this->institution_id, Username: $this->username");
                return [false, "Multiple credentials found, please contact an admin."];
            }
            $user_authentication = $this->available_authentications[$type][0];
            if (!isset($user_authentication->institution_authentication_id)) {
                $special_usernames = Yii::app()->params['special_usernames'] ?? [];
                if (!in_array($this->username, $special_usernames)) {
                    $user_authentication->user->audit('login', 'non-special-user-attempted-special-login', "User Auth id: $user_authentication->id, errors: Non special user attempted special login");
                    return [false, "Invalid login."];
                } else {
                    $this->is_special = true;
                }
            }

            $auth_result = $this->authenticateUser($user_authentication);
            if ($auth_result[0]) {
                $user_authentication->noVersion();
                $user_authentication->last_successful_login_date = date('Y-m-d H:i:s');
                if (!$user_authentication->saveAttributes(['last_successful_login_date'])) {
                    $user_authentication->user->audit('login', 'set-last-successful-login-failed', "User Auth id: $user_authentication->id, errors:" . var_export($user_authentication->getErrors(), true));
                }
                return [true, ""];
            } else {
                return $auth_result;
            }
        }
        return null;
    }

    private function authenticateZendLDAP($user_authentication)
    {
        $ldap_config = $user_authentication->institutionAuthentication->LDAPConfig;
        $user = $user_authentication->user;

        Yii::import('application.vendors.*');
        require_once 'Zend/Ldap.php';

        /*
         * Check with LDAP for authentication
         */
        $options = array(
            'host' => $ldap_config->ldap_server,
            'port' => $ldap_config->ldap_port,
            'username' => $ldap_config->ldap_admin_dn,
            'password' => $ldap_config->ldap_admin_password,
            'baseDn' => $ldap_config->ldap_admin_dn,
            'useStartTls' => false,
        );

        $ldap = $this->getLdap($options);

        /*
         * Try and bind to the login details provided. This indicates if
         * the user is in LDAP.
         */

        try {
            $ldap->bind(
                'cn='.$this->username.','.$ldap_config->ldap_dn,
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

            return [false, "Invalid login."];
        }

        /*
         * User is in LDAP, get their details.
         */
        return [true,
            $ldap->getEntry(
                'cn='.$this->username.','.$ldap_config->ldap_dn,
                array('givenname', 'sn', 'mail')
            )
        ];
    }

    private function authenticateNativeLDAP($user_authentication)
    {
        $ldap_config = $user_authentication->institutionAuthentication->LDAPConfig;
        $user = $user_authentication->user;

        if (preg_match('~ldaps?://~', $ldap_config->ldap_server)) {
            if (!$link = ldap_connect($ldap_config->ldap_server)) {
                OELog::log('Unable to connect to LDAP server: '.$ldap_config->ldap_server);
                return [false, "Invalid login."];
            }
        } else {
            if (!$link = ldap_connect($ldap_config->ldap_server, $ldap_config->ldap_port)) {
                throw new Exception();
                OELog::log('Unable to connect to LDAP server: '.$ldap_config->ldap_server.' '.$ldap_config->ldap_port);
                return [false, "Invalid login."];
            }
        }

        ldap_set_option($link, LDAP_OPT_REFERRALS, 0);
        if ($ldap_config->getLDAPParam('ldap_protocol_version') !== null) {
            ldap_set_option($link, LDAP_OPT_PROTOCOL_VERSION, $ldap_config->getLDAPParam('ldap_protocol_version'));
        }
        ldap_set_option($link, LDAP_OPT_NETWORK_TIMEOUT, $ldap_config->getLDAPParam('ldap_native_timeout'));

        // Bind as the LDAP admin user. Set parameters ldap_admin_dn and ldap_password in local config for this.
        if (!@ldap_bind($link, $ldap_config->ldap_admin_dn, $ldap_config->ldap_admin_password)) {
            $audit = new Audit();
            $audit->action = 'login-failed';
            $audit->target_type = 'login';
            $audit->user_id = $user->id;
            $audit->data = "Login failed for user {$this->username}: LDAP admin bind failed: ".ldap_error($link);
            $audit->save();
            OELog::log("Login failed for user {$this->username}: LDAP admin bind failed: ".ldap_error($link));

            $this->errorCode = self::ERROR_USERNAME_INVALID;

            return [false, "Invalid login."];
        }

        // Perform an LDAP search for the username in order to retrieve their DN. Set the base DN in parameter ldap_dn in local config for this.

        $ldapSearchFilter = '(sAMAccountName='.$this->username.')';

        $ldapSearchResult = ldap_search($link, $ldap_config->ldap_dn, $ldapSearchFilter);

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

            return [false, "Invalid login."];
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

            return [false, "Invalid login."];
        }
        return [true, $info];
    }

    private function authenticateOtherLDAP($user_authentication)
    {
        $ldap_config = $user_authentication->institutionAuthentication->LDAPConfig;
        $user = $user_authentication->user;

        if ($fp = @fsockopen($ldap_config->ldap_server, 389, $errno, $errstr, 5)) {
            if (!$link = ldap_connect($ldap_config->ldap_server)) {
                OELog::log('Unable to connect to LDAP server: '.$ldap_config->ldap_server);
                return [false, "Invalid login."];
            }
        } else {
            throw new Exception('Unable to reach ldap server: '.$ldap_config->ldap_server.': '.$errstr);
            OELog::log('Unable to reach ldap server: '.$ldap_config->ldap_server.': '.$errstr);
            return [false, "Invalid login."];
        }

        ldap_set_option($link, LDAP_OPT_NETWORK_TIMEOUT, $ldap_config->getLDAPParam('ldap_native_timeout'));
        if ($ldap_config->getLDAPParam('ldap_protocol_version') !== null) {
            ldap_set_option($link, LDAP_OPT_PROTOCOL_VERSION, $ldap_config->getLDAPParam('ldap_protocol_version'));
        }
        $ldap_user_prefix = $ldap_config->getLDAPParam('ldap_username_prefix') ?: 'cn';

        if (!@ldap_bind($link, "$ldap_user_prefix=$this->username,".$ldap_config->ldap_dn, $this->password)) {
            $audit = new Audit();
            $audit->action = 'login-failed';
            $audit->target_type = 'login';
            $audit->user_id = $user->id;
            $audit->data = "Login failed for user {$this->username}: LDAP authentication failed: ".ldap_error($link);
            $audit->save();
            OELog::log("Login failed for user {$this->username}: LDAP authentication failed: ".ldap_error($link));

            $this->errorCode = self::ERROR_USERNAME_INVALID;

            return [false, "Invalid login."];
        }

        $attempts = $ldap_config->getLDAPParam('ldap_info_retries') ?? 1;

        for ($i = 0; $i < $attempts; ++$i) {
            if ($i > 0 && $ldap_config->getLDAPParam('ldap_info_retry_delay')) {
                sleep($ldap_config->getLDAPParam('ldap_info_retry_delay'));
            }
            $sr = ldap_search($link, $ldap_config->ldap_dn, "$ldap_user_prefix=$this->username");
            $info = ldap_get_entries($link, $sr);

            if (isset($info[0])) {
                break;
            }
        }

        if (!isset($info[0])) {
            OELog::log("Failed to retrieve ldap info for user $this->username: ".ldap_error($link).' ['.print_r($info, true).']');
            return [false, "Invalid login."];
        }
        return [true, $info[0]];
    }

    /**
     * Authenticates a user.
     *
     * Uses either BASIC or LDAP authentication. BASIC authenticates against
     * the openeyes DB. LDAP uses whichever LDAP is specified in the params.php
     * config file.
     *
     * @return array [0 => bool success, 1 => string message]
     *
     * @throws
     */
    public function authenticateUser($user_authentication, $force = false)
    {
       // if (!in_array(Yii::app()->params['ldap_method'], array('native', 'zend', 'native-search'))) {
       //     throw new Exception('Unsupported LDAP authentication method: '.Yii::app()->params['ldap_method'].', please use native or zend.');
        // }

        $inst_auth = $user_authentication->institutionAuthentication;
        $user = $user_authentication->user;

        if ($user_authentication->active != 1) {
            $user->audit('login', 'login-failed', null, "User not active and so cannot login: $this->username, user id: {$user->id}");
            $this->errorCode = self::ERROR_USER_INACTIVE;

            return [false, "User has been deactivated, please contact an admin."];
        } elseif (!Yii::app()->getAuthManager()->checkAccess('OprnLogin', $user->id)) {
            $user->audit('login', 'login-failed', "User has not been assigned OprnLogin and so cannot login: $this->username, user id: {$user->id}", true);
            $this->errorCode = self::ERROR_USER_INACTIVE;

            return [false, "Invalid login."];
        }


        if (!$this->is_special && $inst_auth->user_authentication_method == 'LDAP') {
            $ldap_config = $inst_auth->LDAPConfig;
            if ($ldap_config->getLDAPParam('utf8_decode_required')) {
                $this->password = utf8_decode($this->password);
            }

            $auth_result = [false, "Invalid login."];
            switch ($ldap_config->ldap_method) {
            case 'zend':
                $auth_result = $this->authenticateZendLDAP($user_authentication);
                break;
            case 'native-search':
                $auth_result = $this->authenticateNativeLDAP($user_authentication);
                break;
            default:
                $auth_result = $this->authenticateOtherLDAP($user_authentication);
            }

            if (!$auth_result[0]) {
                $user->audit('login', 'login-failed', null, "Login failed for user {$this->username}: unable to authenticate with LDAP");
                return $auth_result;
            }
            $info = $auth_result[1];

            /*
             * Update user db record with details from LDAP.
             */
            if ($ldap_config->getLDAPParam('ldap_update_name')) {
                if (isset($info['givenname'][0])) {
                    $user->first_name = trim($info['givenname'][0]);
                }
                if (isset($info['sn'][0])) {
                    $user->last_name = trim($info['sn'][0]);
                }
            }
            if ($ldap_config->getLDAPParam('ldap_update_email')) {
                if (isset($info['mail'][0])) {
                    $user->email = trim($info['mail'][0]);
                }
            }

            if ($user->isModelDirty()) {
                if (!$user->save()) {
                    $message = "Login failed for user {$this->username}: unable to update user with details from LDAP: ".print_r($user->getErrors()) . " First name: [". $user->first_name . "] length: " .
                        strlen($user->first_name)." Last name :[" . $user->last_name . "] length: " . strlen($user->last_name);
                    $user->audit('login', 'login-failed',$message , $message);
                    throw new SystemException('Unable to update user with details from LDAP: '.print_r($user->getErrors() , true) .  $message);
                }
            }
        } elseif ($this->is_special || $inst_auth->user_authentication_method == 'LOCAL') {
            $validPw = $user_authentication->verifyPassword($this->password);

            $is_softlocked =  PasswordUtils::testStatus('softlocked', $user_authentication, $this->is_special) && $user_authentication->password_softlocked_until > date("Y-m-d H:i:s");
            $pwActive = $this->is_special ? true : !(PasswordUtils::testStatus('locked', $user_authentication) || $is_softlocked);

            if (!($validPw && $pwActive)) { //if failed logon or locked
                if(!$this->is_special && (!$validPw || $is_softlocked) && !empty(Yii::app()->params['pw_status_checks']['pw_tries'])){ // if the password was not correct and we check the number of tries
                    PasswordUtils::incrementFailedTries($user_authentication);
                }
                $this->errorCode = self::ERROR_PASSWORD_INVALID;
                $user->audit('login', 'login-failed', null, "Login failed for user {$this->username}: ". ($validPw?'valid password, user auth inactive':'invalid password'));

                return [false, $validPw ? ($is_softlocked ? "User locked, retry in 10 minutes" : "User locked, please contact an admin.") : "Invalid login."];
            }
        } elseif (Yii::app()->params['auth_source'] === 'SAML' || Yii::app()->params['auth_source'] === 'OIDC') {
            // The user is already authenticated from the portal so directly register the username for the session
            $user->username = $this->username;
        } else {
            /*
             * Unknown auth_source, error
             */
            $user->audit('login', 'login-failed', null, "Login failed for user {$this->username}: unknown auth source: ".Yii::app()->params['auth_source']);
            throw new SystemException('Unknown auth_source: '.$inst_auth->user_authentication_method);
        }

        $this->setSessionDataForUser($user, $user_authentication);

        if (!$this->is_special) {
            $institution_name = Institution::model()->findByPk($this->institution_id)->name;
            $site_name = Site::model()->findByPk($this->site_id)->name;

            $user->audit('login',
                'login-successful', null,
                'User ' . strtoupper($this->username) . ' logged in to Institution: ' . strtoupper($institution_name) . ', Site: ' . strtoupper($site_name)
            );
        }

        return [true, ""];
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

    /**
     * Logs the user in case a PIN code was used for login
     * IMPORTANT!! PIN must be verified beforehand as this method
     * does not check PIN.
     *
     * @param User $user
     * @param UserAuthentication $user_authentication
     * @return bool
     */
    public function authenticateWithPIN(User $user, UserAuthentication $user_authentication) : bool
    {
        // PIN already validated at this point
        $this->_id = $user->id;
        $this->errorCode = self::ERROR_NONE;
        try {
            $this->setSessionDataForUser($user, $user_authentication);
        }
        catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            return false;
        }
        $user->audit('login', 'login-successful', null, 'User '.strtoupper($this->username).' logged in');
        return true;
    }

    private function setSessionDataForUser(User $user, UserAuthentication $user_authentication) : void
    {
        $app = Yii::app();
        $this->_id = $user->id;
        $this->username = $user_authentication->username;
        $this->errorCode = self::ERROR_NONE;

        if ($this->is_special) {
            $app->session['user_auth'] = $user_authentication;
            $app->session['user'] = $user_authentication->user;

            $user->audit('login',
                'login-successful', null,
                'Special User '.strtoupper($this->username).' logged in.'
            );
            return;
        }

        // Get all the user's firms and put them in a session

        $firms = array();

        foreach ($user->getAvailableFirms() as $firm) {
            $firms[$firm->id] = $this->firmString($firm);
        }

        if (!count($firms)) {
            $user->audit('login', 'login-failed', null, "Login failed for user {$this->username}: user has no firm rights and cannot use the system");
            throw new Exception('User has no firm rights and cannot use the system.');
        }

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
        if (!empty($this->site_id)) {
            $app->session['selected_site_id'] = $this->site_id;
        } else if ($user->last_site_id) {
            $app->session['selected_site_id'] = $user->last_site_id;
        } elseif ($default_site = Site::model()->getDefaultSite()) {
            $app->session['selected_site_id'] = $default_site->id;
        } else {
            throw new CException('Cannot find default site');
        }

        natcasesort($firms);

        $app->session['user'] = $user;
        $app->session['user_auth'] = $user_authentication;
        $app->session['firms'] = $firms;
        $app->session['selected_institution_id'] = $this->institution_id;

        reset($firms);
    }

}
