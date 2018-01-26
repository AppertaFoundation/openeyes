<?php

class m160525_092545_user extends CDbMigration
{
    public function up()
    {
        $username = (array_key_exists('portal_user', Yii::app()->params)) ? Yii::app()->params['portal_user'] : 'portal_user';
        $this->insert('user', array(
            'username' => $username,
            'first_name' => 'Community',
            'last_name' => 'Portal',
            'email' => 'portal_user@openeyes.com',
            'active' => 1,
            'title' => 'Mr',
            'qualifications' => 'DR',
            'role' => 'OpTom Portal',
            'has_selected_firms' => 0,

        ));
    }

    public function down()
    {
        $username = (array_key_exists('portal_user', Yii::app()->params)) ? Yii::app()->params['portal_user'] : 'portal_user';
        $this->delete('user', 'username = ?', array($username));
    }
}
