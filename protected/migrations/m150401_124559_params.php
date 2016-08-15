<?php

class m150401_124559_params extends CDbMigration
{
    public function up()
    {
        $settings = array(
            array(
                'key' => 'watermark',
                'name' => 'User Banner',
                'data' => '',
                'default_value' => '',
            ),
            array(
                'key' => 'watermark_admin',
                'name' => 'Admin Banner',
                'data' => '',
                'default_value' => '',
            ),
            array(
                'key' => 'helpdesk_email',
                'name' => 'Helpdesk Email',
                'data' => '',
                'default_value' => '',
            ),
            array(
                'key' => 'helpdesk_phone',
                'name' => 'Helpdesk Phone',
                'data' => '',
                'default_value' => '',
            ),
            array(
                'key' => 'alerts_email',
                'name' => 'Alerts Email',
                'data' => '',
                'default_value' => '',
            ),
            array(
                'key' => 'adminEmail',
                'name' => 'Admin Email',
                'data' => '',
                'default_value' => '',
            ),
        );
        $this->insert('setting_field_type', array('name' => 'Text Field'));
        $id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Text Field"')->queryRow();
        if (isset($id['id']) && $id['id']) {
            foreach ($settings as $setting) {
                $setting['field_type_id'] = $id['id'];
                $this->insert('setting_metadata', $setting);
            }
        }
    }

    public function down()
    {
        $id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Text Field"')->queryRow();
        if (isset($id['id']) && $id['id']) {
            $this->delete('setting_metadata', 'field_type_id = "'.$id['id'].'"');
        }
        $this->delete('setting_field_type', 'name = "Text Field"');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
