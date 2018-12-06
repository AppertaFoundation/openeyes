<?php

class m181205_155159_create_icons_table extends OEMigration
{
    public function up()
    {
        $this->createOETable('icons', [
            'id' => 'pk',
            'class_name' => 'varchar(128) COLLATE utf8_bin NOT NULL']);

        $this->insert('icons', array('class_name' => 'exclamation'));
        $this->insert('icons', array('class_name' => 'exclamation-green'));
        $this->insert('icons', array('class_name' => 'exclamation-amber'));
        $this->insert('icons', array('class_name' => 'exclamation-red'));
    }

    public function down()
    {
        $this->dropTable('icons');
    }
}