<?php

class m130912_153500_remove_unneeded_cols_from_user_session extends CDbMigration
{
	public function up()
	{
		$this->dropForeignKey('user_session_last_modified_user_id_fk','user_session');
		$this->dropForeignKey('user_session_created_user_id_fk','user_session');
		$this->dropColumn('user_session','last_modified_user_id');
		$this->dropColumn('user_session','last_modified_date');
		$this->dropColumn('user_session','created_user_id');
		$this->dropColumn('user_session','created_date');
	}

	public function down()
	{
		$this->addColumn('user_session','last_modified_user_id','int(10) unsigned NOT NULL DEFAULT \'1\'');
		$this->addColumn('user_session','last_modified_date','datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'');
		$this->addColumn('user_session','created_user_id','int(10) unsigned NOT NULL DEFAULT \'1\'');
		$this->addColumn('user_session','created_date','datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'');
		$this->addForeignKey('user_session_last_modified_user_id_fk','user_session','last_modified_user_id','user','id');
		$this->addForeignKey('user_session_created_user_id_fk','user_session','created_user_id','user','id');
	}
}
