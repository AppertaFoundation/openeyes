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
 * @copyright Copyright (C) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Reset OE User Auth Passwords in the event that the user is locked out.
 */
class ResetUserLockCommand extends CConsoleCommand {
    public $defaultAction = 'reset';

    public function getName() {
        return 'Reset OE User Auth Passwords in the event of the user being locked out';
    }

    public function getHelp() {
        return "reset lock on login for a user\n" .
            "yiic resetuserlock --password='password' --username='username'\n\n".
            "create institution and user authentications for a user\n".
            "yiic resetuserlock createauthforinstitutions --ids_str='1,2,3' --username='username'\n\n";
    }

    /**
     * Create the required Institution Authentication and User Authentication for 
     * logging in via LOCAL authentication, if they do not already exist for the 
     * given username and given institution ids.
     */
    public function actionCreateAuthForInstitutions($ids_str = "", $username="admin")
    {
        $ids = explode(",", $ids_str);
        foreach ($ids as $id) {
            $institution = Institution::model()->findByPk($id);
            if (!$institution) {
                echo "Institution not found for id: $id.";
                break;
            }

            $inst_auths = InstitutionAuthentication::model()->findAllByAttributes([ 'institution_id' => $id, 'user_authentication_method' => 'LOCAL' ]);
            if (!empty($inst_auths)) {
                echo count($inst_auths)." LOCAL institution authentications for institution id: $id already exist.";
            } else {
                echo "Creating a LOCAL institution authentication for institution id: $id.";
                $new_inst_auth = InstitutionAuthentication::newFromInstitution($id);
                $new_inst_auth->user_authentication_method = 'LOCAL';
                $new_inst_auth->description = "Auto-generated LOCAL auth for $institution->name.";
                if (!$new_inst_auth->save()) {
                    echo "Error creating LOCAL institution authentication for institution id: $id, details: ".var_dump($new_inst_auth->getErrors());
                    break;
                }
                $inst_auths = [$new_inst_auth];
            }

            foreach ($inst_auths as $inst_auth) {
                if (UserAuthentication::model()->countByAttributes([ 'institution_authentication_id' => $inst_auth->id ]) > 0) {
                    echo "User authentication already exists for institution authentication: $inst_auth->id.";
                } else {
                    echo "Creating user authentication for institution authentication: $inst_auth->id.";
                    $new_user_auth = new UserAuthentication();
                    $new_user_auth->institution_authentication_id = $inst_auth->id;
                    $new_user_auth->user_id = 1;
                    $new_user_auth->username = "admin";
                    $new_user_auth->password = "TEMPpassword1!";
                    $new_user_auth->password_repeat = "TEMPpassword1!";
                    if (!$new_user_auth->save()) {
                        echo "Error creating admin user authentication for institution authentication id: $inst_auth->id, details: ".var_dump($new_user_auth->getErrors());
                    }
                }
            }
            echo "DONE";
        }
    }

    /**
     * Reset the given user, with password if specified.
     *
     * @param $filename
     */
    public function actionReset($password = null, $username="")
    {
        $local_user_auths = array_filter(
            UserAuthentication::model()->findAllByAttributes([ 'username' => $username ]),
            function ($user_auth) { return $user_auth->institutionAuthentication->user_authentication_method == 'LOCAL'; }
        );
        if (empty($local_user_auths)) {
            echo "No user auths found for username: $username.";
        }

        foreach ($local_user_auths as $user_auth) {
            $user_auth->password_status = 'current';
            $user_auth->password_failed_tries = 0;
            $user_auth->password_last_changed_date = date("Y-m-d H:i:s");
            $user_auth->password_softlocked_until = date("Y-m-d H:i:s");

            if ($password) {
                $user_auth->password = $password;
                $user_auth->password_repeat = $password;
                if (!$user_auth->save()) {
                    $user_auth->user->audit('ResetUserLockCommand', 'Problems resetting password for user '.$user_auth->user->id.' for user auth '.$user_auth->id.' details:'.var_dump($user_auth->getErrors()));
                    echo var_dump($user_auth->getErrors());
                } else {
                    $user_auth->user->audit('ResetUserLockCommand', 'Password for user_id '.$user_auth->user->id.' for user auth '.$user_auth->id.' has been reset');
                    echo 'password for '.$user_auth->username." has been reset";
                }
            } else {
                if (!$user_auth->saveAttributes(['password_status','password_failed_tries','password_last_changed_date'])) {
                    if($user_auth->getErrors()){
                        $user_auth->user->audit('ResetUserLockCommand', 'Problems unlocking user_id '.$user_auth->user->id.' for user auth '.$user_auth->id.' with errors: '.var_dump($user_auth->getErrors()));
                        echo var_dump($user_auth->getErrors());
                    }
                    else{
                        $user_auth->user->audit('ResetUserLockCommand', 'The user for user_id '.$user_auth->user->id.' for user auth '.$user_auth->id.' is already unlocked.');
                        echo 'warning! '.$user_auth->username.' is already unlocked.';
                    }
                } else {
                    $user_auth->audit('ResetUserLockCommand', 'The user for user_id '.$user_auth->id.' for user auth '.$user_auth->id.' is now unlocked');
                    echo 'the user for '.$user_auth->username.' is now unlocked.';
                }
            }
        }
    }
}
