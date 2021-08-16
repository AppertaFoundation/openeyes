<?php
/**
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class PasswordUtils
{
    private static $password_statuses = [
        'locked' => 0,
        'softlocked' => 1,
        'expired' => 2,
        'current' => 3,
        'stale' => 4,
    ];

    /**
     * @return array
     */
    public static function getPasswordRestrictions()
    {
        $pw_restrictions = Yii::app()->params['pw_restrictions'];

        if ($pw_restrictions===null) {
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
            $pw_restrictions['min_length_message'] = 'Passwords must be at least '.$pw_restrictions['min_length'].' characters long';
        }
        if (!isset($pw_restrictions['max_length'])) {
            $pw_restrictions['max_length'] = 70;
        }
        if (!isset($pw_restrictions['max_length_message'])) {
            $pw_restrictions['max_length_message'] = 'Passwords must be at most '.$pw_restrictions['max_length'].' characters long';
        }
        if (!isset($pw_restrictions['strength_regex'])) {
            $pw_restrictions['strength_regex'] = "%.*%";
        }
        if (!isset($pw_restrictions['strength_message'])) {
            $pw_restrictions['strength_message'] = "N/A";
        }
        return $pw_restrictions;
    }

    public static function randomSalt()
    {
        $salt = '';
        for ($i = 0; $i < 10; ++$i) {
            switch (rand(0, 2)) {
                case 0:
                    $salt .= chr(rand(48, 57));
                    break;
                case 1:
                    $salt .= chr(rand(65, 90));
                    break;
                case 2:
                    $salt .= chr(rand(97, 122));
                    break;
            }
        }

        return $salt;
    }

    /**
     * Returns an md5 hash of the password and username provided.
     *
     * @param string $password
     * @param string $salt
     *
     * @return string
     */
    public static function hashPassword($password, $salt)
    {
        if (!$salt) {
            return password_hash($password, PASSWORD_BCRYPT);
        }
        return md5($salt . $password);
    }

    public static function testStatus($status = 'locked', $user_authentication, $is_special = false)
    {
        if (!$is_special && $user_authentication->institutionAuthentication->user_authentication_method != 'LOCAL') {
            return null;
            //throw exception?
        }

        if ($user_authentication->password_status == $status) {
            return true;
        }
        if ($status == 'locked' && !array_key_exists($user_authentication->password_status, self::$password_statuses)) {
            return true;
        }
        return false;
    }

    public static function setHarsherStatus($status, $user_authentication, $save = true)
    {
        //same exception as above

        if (!array_key_exists($status, self::$password_statuses)) {
            return;
        }
        if (self::$password_statuses[$status] <= self::$password_statuses[$user_authentication->password_status]) {
            $user_authentication->password_status = $status;

            if (PasswordUtils::testStatus('softlocked', $user_authentication)) {
                $temp_now = new DateTime();
                $pw_timeout = !empty(Yii::app()->params['pw_status_checks']['pw_softlock_timeout']) ? Yii::app()->params['pw_status_checks']['pw_softlock_timeout'] : '10 mins';
                $user_authentication->password_softlocked_until = date_format(date_add($temp_now, date_interval_create_from_date_string($pw_timeout)), "Y-m-d H:i:s");
                $user_authentication->saveAttributes(['password_softlocked_until']);
            }

            return $save
                ? $user_authentication->saveAttributes(['password_status'])
                : true;
        }
        return false;
    }

    public static function incrementFailedTries($user_authentication)
    {
        //same exception

        $max_allowed_tries = Yii::app()->params['pw_status_checks']['pw_tries'] ?: 3;
        $max_exceeded_status = Yii::app()->params['pw_status_checks']['pw_tries_failed'] ?: 'locked';
        $max_exceeded_status = array_key_exists($max_exceeded_status, self::$password_statuses) ? $max_exceeded_status : 'locked';
        $user_authentication->password_failed_tries++;
        $max_reached = false;

        if ($user_authentication->password_failed_tries >= $max_allowed_tries) {
            $user_authentication->password_failed_tries = $max_allowed_tries;
            self::setHarsherStatus($max_exceeded_status, $user_authentication);
            $max_reached = true;
        }

        $user_authentication->saveAttributes(['password_failed_tries']);
        return $max_reached;
    }

    public static function testPasswordExpiry($user_authentication)
    {
        // Special Local users (eg. docman_user, api, etc.) should not be subject to password expiry.
        $local_users = Yii::app()->params['local_users'] ?? [];
        if (in_array($user_authentication->username, $local_users)) {
          return true;
        }

        $expiry_statuses = ['lock' => '45 days', 'expire' => '30 days', 'stale' => '15 days'];

        $last_changed_date = $user_authentication->password_last_changed_date ?? date("Y-m-d H:i:s");
        if (!$last_changed_date) {
            return false;
        }

        foreach ($expiry_statuses as $status => $default_no_of_days) {
            $no_of_days = Yii::app()->params['pw_status_checks']["pw_days_$status"] ?? $default_no_of_days;
            if ($no_of_days) {
                $status_date = date("Y-m-d H:i:s", strtotime($last_changed_date . '+' . $no_of_days));
                if (date("Y-m-d H:i:s") >= $status_date) {
                    return self::setHarsherStatus($status, $user_authentication);
                }
            }
        }
        return false;
    }

    public static function getDaysLeft($user_authentication)
    {
        //same excepti
        $expiry_statuses = ['lock' => '45 days', 'expire' => '30 days', 'stale' => '15 days'];

        $last_changed_date = $user_authentication->password_last_changed_date ?? date("Y-m-d H:i:s");
        if (!$last_changed_date) {
            return [];
        }
        $last_changed_date = date_create($last_changed_date);
        $days_left = [];

        foreach ($expiry_statuses as $status => $default_no_of_days) {
            $no_of_days = isset(Yii::app()->params['pw_status_checks']["pw_days_$status"]) ?? $default_no_of_days;
            if ($no_of_days) {
                $status_date = date_create(date($last_changed_date, strtotime('+' . $no_of_days)));
                $days_left[$status] = date_diff($last_changed_date, $status_date)->format('%a days');
            }
        }
        return $days_left;
    }

    public static function getDisplayStatus($user_authentication)
    {
        return array_key_exists($user_authentication->password_status, self::$password_statuses)
            ? ucwords($user_authentication->password_status)
            : null;
    }
}
