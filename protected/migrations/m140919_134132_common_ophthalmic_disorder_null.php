<?php

class m140919_134132_common_ophthalmic_disorder_null extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('common_ophthalmic_disorder','disorder_id','int(10) unsigned null');
		$this->alterColumn('common_ophthalmic_disorder_version','disorder_id','int(10) unsigned null');
	}

	public function down()
	{
		$this->alterColumn('common_ophthalmic_disorder','disorder_id','int(10) unsigned not null');
		$this->alterColumn('common_ophthalmic_disorder_version','disorder_id','int(10) unsigned not null');
	}
}
