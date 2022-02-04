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
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
    public $username;
    public $password;
    public $institution_id;
    public $site_id;
    public $rememberMe;

    private $_identity;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            // username and password are required
            array('institution_id', 'requiredIfInstitutionRequired'),
            array('username, password, site_id', 'required'),
            array('institution_id', 'exist', 'allowEmpty' => true, 'attributeName' => 'id', 'className' => Institution::class),
            array('site_id', 'exist', 'allowEmpty' => true, 'attributeName' => 'id', 'className' => Site::class),
            // rememberMe needs to be a boolean
            array('rememberMe', 'boolean'),
            // password needs to be authenticated
            array('password', 'authenticate'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'rememberMe' => 'Remember me next time',
        );
    }

    private function getDefaultInstitution()
    {
        return \Institution::model()->findByAttributes([ 'remote_id' => Yii::app()->params['institution_code'] ]);
    }

    public function beforeValidate()
    {
        if (empty($this->institution_id) && SettingMetadata::model()->getSetting('institution_required') == 'off' && isset(Yii::app()->params['institution_code'])) {
            $institution = $this->getDefaultInstitution();
            if (isset($institution)) {
                $this->institution_id = $institution->id;
            }
        }
        if ($this->username === Yii::app()->params['docman_user']) {
            if (empty($this->institution_id)) {
                $default_institution = $this->getDefaultInstitution();
                if (!$default_institution) {
                    throw new Exception("No default institution specified");
                }
                $this->institution_id = $default_institution->id;
            }
            if (empty($this->site_id)) {
                $institution = \Institution::model()->findByPk($this->institution_id);
                $this->site_id = $institution->first_used_site_id ?? $institution->sites[0]->id;
            }
        }
        return parent::beforeValidate();
    }

    /**
     * Requires institution_id only if Institution is required to log in
     */
    public function requiredIfInstitutionRequired($attribute, $params)
    {
        if (empty($this->institution_id)) {
            if (SettingMetadata::model()->getSetting('institution_required') == 'on') {
                $this->addError($attribute, 'An institution must be selected');
            } elseif (isset(Yii::app()->params['institution_code'])) {
                $this->addError($attribute, 'Default institution not found');
            } else {
                $this->addError($attribute, 'Default institution code must be set');
            }
        }
    }

    /**
     * Set Institution and site cookie/site first use.
     */
    private function persistInstitutionAndSite()
    {
        // Set institutions default site if not already set
        $institution = \Institution::model()->findByPk($this->institution_id);
        if (isset($institution) && !isset($institution->first_used_site_id)) {
            $institution->first_used_site_id = $this->site_id;
            $institution->save();
        }

        // Set institution and site cookies
        Yii::app()->request->cookies['current_institution_id'] = new CHttpCookie('current_institution_id', $this->institution_id);
        Yii::app()->request->cookies['current_site_id'] = new CHttpCookie('current_site_id', $this->site_id);
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->_identity = new UserIdentity($this->username, $this->password, $this->institution_id, $this->site_id);
            $auth_result = $this->_identity->authenticate();

            if (!$auth_result[0]) {
                $this->addError('username', $auth_result[1]);
                $this->addError('password', $auth_result[1]);
            }
        }
    }

    /**
     * Logs in the user using the given username and password in the model.
     *
     * @return bool whether login is successful
     */
    public function login()
    {
        if ($this->_identity === null) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            $this->_identity->authenticate();
        }
        if ($this->_identity->isAuthenticated) {
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days
            Yii::app()->user->login($this->_identity, $duration);
            $this->persistInstitutionAndSite();
            return true;
        } else {
            return false;
        }
    }
}
