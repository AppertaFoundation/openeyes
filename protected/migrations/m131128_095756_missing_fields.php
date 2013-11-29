<?php

class m131128_095756_missing_fields extends CDbMigration
{
	//public $tables = array('anaesthetic_type','disorder_tree','element_type_anaesthetic_type','element_type_eye','element_type_priority','event_group','eye','priority','specialty');
	public $tables = array('anaesthetic_type','disorder_tree','element_type_anaesthetic_type','element_type_eye','element_type_priority','eye','priority','specialty');

	public function up()
	{
		foreach ($this->tables as $table) {
			$this->addColumn($table,"created_user_id","int(10) unsigned NOT NULL");
			$this->update($table,array('created_user_id'=>1));

			$this->createIndex("{$table}_created_user_id_fk",$table,"created_user_id");
			$this->addForeignKey("{$table}_created_user_id_fk",$table,"created_user_id","user","id");

			$this->addColumn($table,"last_modified_user_id","int(10) unsigned NOT NULL");
			$this->update($table,array('last_modified_user_id'=>1));

			$this->createIndex("{$table}_last_modified_user_id_fk",$table,"last_modified_user_id");
			$this->addForeignKey("{$table}_last_modified_user_id_fk",$table,"last_modified_user_id","user","id");

			$this->addColumn($table,"created_date","date not null default '1900-01-01 00:00:00'");
			$this->addColumn($table,"last_modified_date","date not null default '1900-01-01 00:00:00'");
		}
	}

	public function down()
	{
		foreach ($this->tables as $table) {
			$this->dropForeignKey("{$table}_created_user_id_fk",$table);
			$this->dropIndex("{$table}_created_user_id_fk",$table);
			$this->dropColumn($table,"created_user_id");

			$this->dropForeignKey("{$table}_last_modified_user_id_fk",$table);
			$this->dropIndex("{$table}_last_modified_user_id_fk",$table);
			$this->dropColumn($table,"last_modified_user_id");

			$this->dropColumn($table,"created_date");
			$this->dropColumn($table,"last_modified_date");
		}
	}
}
