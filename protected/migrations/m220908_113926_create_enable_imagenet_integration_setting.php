<?php

class m220908_113926_create_enable_imagenet_integration_setting extends OEMigration
{
    public function safeUp()
    {
        $field_type_id = $this->dbConnection
            ->createCommand()->select('id')->from('setting_field_type')->where('name = :name',
                [':name' => 'Radio buttons'])->queryScalar();

        $this->insert('setting_metadata', [
            'display_order' => 0,
            'field_type_id' => $field_type_id,
            'key' => 'enable_imagenet_integration',
            'name' => 'Imagenet: enable integration',
            'default_value' => 'off',
            'data' => serialize(['on'=>'On', 'off'=>'Off'])
        ]);

        $this->delete('setting_installation', '`key` = "imagenet_url"');
        $this->delete('setting_metadata', '`key` = "imagenet_url"');
    }

    public function safeDown()
    {
        $text_field_type_id = $this->dbConnection
            ->createCommand()->select('id')->from('setting_field_type')->where('name = :name',
                [':name' => 'Text Field'])->queryScalar();

        $this->delete('setting_installation', "`key` = 'enable_imagenet_integration'");
        $this->insert('setting_metadata', [
            'element_type_id' => null,
            'display_order' => 26,
            'field_type_id' => $text_field_type_id,
            'key' => 'imagenet_url',
            'name' => 'ImageNET URL',
            'data' => '',
            'default_value' => ''
        ]);
        $this->insert('setting_installation', [
            'key' => 'imagenet_url',
            'value' => ''
        ]);
    }
}
