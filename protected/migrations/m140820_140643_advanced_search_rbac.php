<?php

class m140820_140643_advanced_search_rbac extends CDbMigration
{
	public function up()
	{
		$this->insert('authitem',array('name'=>'OprnAdvancedSearch', 'type' => 0));
	}

	public function down()
	{

	}
}