<?php

class m120106_183243_last_modified_and_created_fields_for_date_letter_sent_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('date_letter_sent','last_modified_user_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addColumn('date_letter_sent','last_modified_date','datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'');
		$this->addForeignKey('date_letter_sent_last_modified_user_id_fk','date_letter_sent','last_modified_user_id','user','id');
		$this->addColumn('date_letter_sent','created_user_id','int(10) unsigned NOT NULL DEFAULT 1');
		$this->addForeignKey('date_letter_sent_created_user_id_fk','date_letter_sent','created_user_id','user','id');
		$this->addColumn('date_letter_sent','created_date','datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'');
	}

	public function down()
	{
		$this->dropForeignKey('date_letter_sent_created_user_id_fk','date_letter_sent');
		$this->dropForeignKey('date_letter_sent_last_modified_user_id_fk','date_letter_sent');
		$this->dropColumn('date_letter_sent','created_date');
		$this->dropColumn('date_letter_sent','created_user_id');
		$this->dropColumn('date_letter_sent','last_modified_date');
		$this->dropColumn('date_letter_sent','last_modified_user_id');
	}
}
