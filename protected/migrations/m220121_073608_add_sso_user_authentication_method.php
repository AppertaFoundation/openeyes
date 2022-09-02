<?php

class m220121_073608_add_sso_user_authentication_method extends OEMigration
{
    public function safeUp()
    {
        $authentication = Yii::app()->db->createCommand()
            ->select('code')
            ->from('user_authentication_method')
            ->where('code = "SSO"')
            ->queryScalar();

        if (!$authentication) {
            $this->insert('user_authentication_method', [
                'code' => 'SSO',
            ]);
        }
    }

    public function safeDown()
    {
        echo "m220121_073608_add_sso_user_authentication_method does not support migration down.\n";
        return true;
    }
}
