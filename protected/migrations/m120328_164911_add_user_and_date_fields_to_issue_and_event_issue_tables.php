<?php

class m120328_164911_add_user_and_date_fields_to_issue_and_event_issue_tables extends CDbMigration
{
	public function up()
	{
		$this->addColumn('event_issue','last_modified_user_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addColumn('event_issue','last_modified_date','datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'');
		$this->addForeignKey('event_issue_last_modified_user_id_fk','event_issue','last_modified_user_id','user','id');
		$this->addColumn('event_issue','created_user_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addForeignKey('event_issue_created_user_id_fk','event_issue','created_user_id','user','id');
		$this->addColumn('event_issue','created_date','datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'');

		$this->addColumn('issue','last_modified_user_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addColumn('issue','last_modified_date','datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'');
		$this->addForeignKey('issue_last_modified_user_id_fk','issue','last_modified_user_id','user','id');
		$this->addColumn('issue','created_user_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addForeignKey('issue_created_user_id_fk','issue','created_user_id','user','id');
		$this->addColumn('issue','created_date','datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'');
	}

	public function down()
	{
		$this->dropForeignKey('event_issue_created_user_id_fk','event_issue');
		$this->dropForeignKey('event_issue_last_modified_user_id_fk','event_issue');
		$this->dropColumn('event_issue','created_date');
		$this->dropColumn('event_issue','created_user_id');
		$this->dropColumn('event_issue','last_modified_date');
		$this->dropColumn('event_issue','last_modified_user_id');

		$this->dropForeignKey('issue_created_user_id_fk','issue');
		$this->dropForeignKey('issue_last_modified_user_id_fk','issue');
		$this->dropColumn('issue','created_date');
		$this->dropColumn('issue','created_user_id');
		$this->dropColumn('issue','last_modified_date');
		$this->dropColumn('issue','last_modified_user_id');
	}
}
