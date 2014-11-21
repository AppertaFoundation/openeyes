<?php

class m141120_162720_alternate_common_opthalmic_disorder_label extends CDbMigration
{
	public function up()
	{
		$this->addColumn('common_ophthalmic_disorder','alternate_disorder_label','varchar(255) NULL DEFAULT NULL');
		$this->addColumn('common_ophthalmic_disorder_version','alternate_disorder_label','varchar(255) NULL DEFAULT NULL');
	}

	public function down()
	{
		$this->dropColumn('common_ophthalmic_disorder','alternate_disorder_label');
		$this->dropColumn('common_ophthalmic_disorder_version','alternate_disorder_label');
	}

}