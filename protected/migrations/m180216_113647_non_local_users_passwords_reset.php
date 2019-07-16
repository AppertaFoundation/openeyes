<?php

class m180216_113647_non_local_users_passwords_reset extends CDbMigration
{
    public function up()
    {

        $dataProvider = new CActiveDataProvider('User');
        $user_iterator = new CDataProviderIterator($dataProvider);

        if( \Yii::app()->params['auth_source'] == 'LDAP' ){

            echo "\n\n LDAP aauthentication is enabled, resetting all non local users' password\n\n";

            foreach ($user_iterator as $user) {
                if($user->is_local) continue;
                $password = $user->generateRandomPassword();
                $user->password = $password;
                $user->password_repeat = $password;

                if($user->save()){
                    \OELog::log($user->username . "'s' password has been reset.");
                }
            }
        }
    }

    public function down()
    {
        echo "m180216_113647_non_local_users_passwords_reset does not support migration down.\n";
        return false;
    }
}