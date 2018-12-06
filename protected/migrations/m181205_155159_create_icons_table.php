<?php

class m181205_155159_create_icons_table extends OEMigration
{
	public function up()
	{
        $this->createOETable('icons', [
            'id' => 'pk',
            'class_name' => 'varchar(128) COLLATE utf8_bin NOT NULL']);

        $this->getDbConnection()->getCommandBuilder()->createMultipleInsertCommand(
            'icons',
            array(
                array('class_name' => 'exclamation'),
                array('class_name' => 'exclamation-green'),
                array('class_name' => 'exclamation-amber'),
                array('class_name' => 'exclamation-red'),
            )
        )->execute();
	}

	public function down()
	{
		$this->dropTable('icons');
	}
}