<?php

class m181120_175400_add_complog_port_to_config extends CDbMigration
{
	public function up()
	{
        $settings = array(
            array(
                'key' => 'COMPLog_port',
                'name' => 'COMPLog tcp port number (0=off)',
                'data' => '',
                'default_value' => '0'
            ),
        );
        $id = $this->getDbConnection()->createCommand('select id from setting_field_type where name ="Text Field"')->queryRow();
        if (isset($id['id']) && $id['id']) {
            foreach ($settings as $setting) {
                $setting['field_type_id'] = $id['id'];
                $this->insert('setting_metadata', $setting);
            }
        }
        $this->insert('setting_installation', array('key' => 'COMPLog_port', 'value' => '0'));
	}
}
