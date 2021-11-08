<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * LoginFormMobileDevice class.
 * LoginFormMobileDevice is the data structure for keeping
 * user login form data. It is used on mobile devices by the 'login' action of 'SiteController'.
 */
class LoginFormMobileDevice extends CFormModel
{
    public $pin;
    public $username;
    public $user_id;
    public $institution_id;
    public $site_id;
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
            array('user_id, pin', 'required'),
        );
    }

    /**
     * Logs in the user using the given user id and PIN code in the model.
     *
     * @return bool whether login is successful
     */
    public function login()
    {
        $user = User::model()->findByPk($this->user_id);
        $user_authentication = null;
        if($user && $user->checkPin($this->pin, $this->user_id, $this->institution_id, $this->site_id, $user_authentication)) {
            if ($this->_identity === null) {
                $this->_identity = new UserIdentity($this->username, null, $this->institution_id, $this->site_id, $this->pin);
                $this->_identity->authenticateWithPIN($user, $user_authentication);
            }

            if ($this->_identity->isAuthenticated) {
                Yii::app()->user->login($this->_identity);
                return true;
            } else {
                return false;
            }
        }
        else {
            $this->addError("pin", "Invalid PIN, please try again.");
            $this->pin = "";
            return false;
        }
    }

}
