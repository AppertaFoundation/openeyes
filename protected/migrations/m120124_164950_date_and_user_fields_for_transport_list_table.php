<?php

class m120124_164950_date_and_user_fields_for_transport_list_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('transport_list','created_user_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addColumn('transport_list','last_modified_user_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addForeignKey('transport_list_created_user_id_fk','transport_list','created_user_id','user','id');
		$this->addForeignKey('transport_list_last_modified_user_id_fk','transport_list','created_user_id','user','id');
		$this->addColumn('transport_list','created_date',"datetime NOT NULL DEFAULT '1900-01-01 00:00:00'");
		$this->addColumn('transport_list','last_modified_date',"datetime NOT NULL DEFAULT '1900-01-01 00:00:00'");
	}

	public function down()
	{
		$this->dropColumn('transport_list','created_date');
		$this->dropColumn('transport_list','last_modified_date');
		$this->dropForeignKey('transport_list_created_user_id_fk','transport_list');
		$this->dropForeignKey('transport_list_last_modified_user_id_fk','transport_list');
		$this->dropColumn('transport_list','last_modified_user_id');
		$this->dropColumn('transport_list','created_user_id');
	}
}
