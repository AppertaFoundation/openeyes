<?php

class m190129_095640_rename_macula_parent_to_retina extends CDbMigration
{
	public function up()
	{
        $this->update('element_group', array('name' => 'Retina'), "name = 'Macula'");
    }

	public function down()
	{
        $this->update('element_group', array('name' => 'Macula'), "name = 'Retina'");
	}
}