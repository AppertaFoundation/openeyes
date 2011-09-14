<?php

class m110826_124603_alter_user_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('user', 'code', 'varchar(255) DEFAULT NULL');
		$this->dropColumn('user', 'password');
		$this->dropColumn('user', 'salt');
                $this->addColumn('user', 'password', 'varchar(40) COLLATE utf8_bin DEFAULT NULL');
                $this->addColumn('user', 'salt', 'varchar(10) COLLATE utf8_bin DEFAULT NULL');

		$this->update('user', array('password' => 'd45409ef1eaa57f5041bf3a1b510097b', 'salt' => 'FbYJis0YG3'));
	}

	public function down()
	{
		$this->dropColumn('user', 'code');
                $this->dropColumn('user', 'password');
                $this->dropColumn('user', 'salt');
                $this->addColumn('user', 'password', 'varchar(40) COLLATE utf8_bin NOT NULL');
                $this->addColumn('user', 'salt', 'varchar(10) COLLATE utf8_bin NOT NULL');

		$this->update('user', array('password' => 'd45409ef1eaa57f5041bf3a1b510097b', 'salt' => 'FbYJis0YG3'));
	}
}
