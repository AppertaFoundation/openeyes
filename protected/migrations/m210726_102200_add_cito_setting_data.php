<?php

class m210726_102200_add_cito_setting_data extends OEMigration
{
    public function safeUp()
    {
        $exists_meta_data = $this->dbConnection->createCommand()->select('id')->from('setting_metadata')->where('`key` = :setting_key', array(':setting_key' => 'cito_base_url'))->queryScalar();

        if (!$exists_meta_data) {
            $this->insert('setting_metadata', array(
                'element_type_id' => null,
                'display_order' => 22,
                'field_type_id' => 4,
                'key' => 'cito_base_url',
                'name' => 'Cito base URL',
                'data' => '',
                'default_value' => ''
            ));
            $this->insert('setting_installation', [
                'key' => 'cito_base_url',
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete('setting_metadata', '`key` = ?', array('cito_base_url'));
        $this->delete('setting_installation', '`key` = ?', array('cito_base_url'));
    }
}
