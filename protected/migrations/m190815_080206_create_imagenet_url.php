<?php

class m190815_080206_create_imagenet_url extends CDbMigration
{
    public function up()
    {
        $this->insert('setting_metadata', array(
            'element_type_id' => null,
            'display_order' => 26,
            'field_type_id' => 4,
            'key' => 'imagenet_url',
            'name' => 'ImageNET URL',
            'data' => '',
            'default_value' => ''
        ));
        $this->insert('setting_installation', array(
            'key' => 'imagenet_url',
            'value' => ''
        ));
    }

    public function down()
    {
        $this->delete('setting_installation', 'key = :key', [':key' => 'imagenet_url']);
        $this->delete('setting_metadata', 'key = :key', [':key' => 'imagenet_url']);
    }
}
