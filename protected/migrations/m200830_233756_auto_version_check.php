<?php

class m200830_233756_auto_version_check extends CDbMigration
{
    public function safeUp()
    {
        $field_type_id = $this->dbConnection->createCommand()->select('id')->from('setting_field_type')->where(
            'name = :name',
            array(':name' => 'Radio buttons')
        )->queryScalar();

        $this->insert('setting_metadata', array(
            'key' => 'auto_version_check',
            'name' => 'Automatic version check',
            'data' => serialize(['disable' => 'Disable', 'enable' => 'Enable']),
            'field_type_id' => $field_type_id,
            'default_value' => 'disable',
        ));

        $this->insert('setting_installation', [
            'key' => 'auto_version_check',
            'value' => 'disable',
        ]);
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = "auto_version_check"');
        $this->delete('setting_installation', '`key` = "auto_version_check"');
    }
}
