<?php

class m111007_160831_add_session_comments extends CDbMigration
{
	public function up()
	{
		$this->addColumn('session', 'comments', 'text COLLATE utf8_bin');
	}

	public function down()
	{
		$this->dropColumn('session', 'comments');
	}
}