<?php

class m181205_155159_create_icons_table extends OEMigration
{
    public function up()
    {
        $this->createOETable('icons', [
            'id' => 'pk',
            'class_name' => 'varchar(128) COLLATE utf8_bin NOT NULL',
            'banner_class_name' => 'varchar(128) COLLATE utf8_bin NULL',]);

        $this->insert('icons', array('class_name' => 'exclamation pro-theme' , 'banner_class_name' => 'info'));
        $this->insert('icons', array('class_name' => 'exclamation-green' , 'banner_class_name' => 'success'));
        $this->insert('icons', array('class_name' => 'exclamation-amber' , 'banner_class_name' => 'issue'));
        $this->insert('icons', array('class_name' => 'exclamation-red' , 'banner_class_name' => 'patient'));
    }

    public function down()
    {
        $this->dropTable('icons');
    }
}
