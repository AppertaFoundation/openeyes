<?php

class m120615_134904_drop_element_type_name_field_unique_key extends CDbMigration
{
	public function up()
	{
		$this->dropIndex('name','element_type');
	}

	public function down()
	{
		$this->createIndex('name','element_type','name',true);
	}
}
