<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Generate a .dot graph file from the RBAC authitems in the database.
 */
class ResetUserLockCommand extends CConsoleCommand {
    public $defaultAction = 'reset';

    public function getName() {
        return 'Reset OE User Passwords in the event critical user locked out';
    }

    public function getHelp() {
        return "yiic resetuserlock --user=<user>\n\n".
        "reset lock on login for user <user>";
    }

    /**
  * Default action to process the given configuration file.
  *
  * @param $filename
  */
    public function actionReset($pw = null)
    {
        if (Yii::app()->params['auth_source'] === 'BASIC') {
            $username='admin';
            $user = User::model()->find('username=?',array($username));
            if($user){                
                echo "found user:" . $user->username."\n";
                $user->password_status = 'current';
                $user->password_failed_tries = 0;
                $user->password_softlocked_until = date("Y-m-d H:i:s");
                $user->password_last_changed_date = date("Y-m-d H:i:s");
                if($pw){
                    $user->password = $pw;
                    $user->password_repeat = $pw;
                    $user->password_hashed = false;
                    if (!$user->save()) {
                        $user->audit('ResetUserLockCommand', 'Problems resetting password for user '.$user->id.' details:'.var_dump($user->getErrors()));
                        echo var_dump($user->getErrors());
                    } else {
                        $user->audit('ResetUserLockCommand', 'Password for user_id '.$user->id.' has been reset');
                        echo 'password for '.$user->username." has been reset";
                    }
                }                
                else{                
                    if (!$user->saveAttributes(array('password_status','password_failed_tries','password_last_changed_date'))) {
                        if($user->getErrors()){
                            $user->audit('ResetUserLockCommand', 'Problems unlocking user_id '.$user->id.' with errors: '.var_dump($user->getErrors()));
                            echo var_dump($user->getErrors());
                        }
                        else{
                            $user->audit('ResetUserLockCommand', 'The user for user_id '.$user->id.' is already unlocked.');
                            echo 'warning! '.$user->username.' is already unlocked.';
                        }
                    } else {
                        $user->audit('ResetUserLockCommand', 'The user for user_id '.$user->id.' is now unlocked');
                        echo 'the user for '.$user->username.' is now unlocked.';
                    }
                }
            }
            else{
                $user->audit('ResetUserLockCommand', 'Could not find user '. $username);
                echo "could not find user " . $username;
            }
        }
    }
}
