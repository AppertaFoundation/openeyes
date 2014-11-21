<?php

class m141120_142152_primary_common_ophthalmic_disorder extends CDbMigration
{
	public function up()
	{
		$this->addColumn('common_ophthalmic_disorder','alternate_disorder_id','int(10) UNSIGNED NULL DEFAULT NULL');
		$this->addColumn('common_ophthalmic_disorder_version','alternate_disorder_id','int(10) UNSIGNED NULL DEFAULT NULL');
		$this->addForeignKey('common_ophthalmic_disorder_ibfk_3','common_ophthalmic_disorder','alternate_disorder_id','disorder','id');
	}

	public function down()
	{
		$this->dropForeignKey('common_ophthalmic_disorder_ibfk_3','common_ophthalmic_disorder');
		$this->dropColumn('common_ophthalmic_disorder','alternate_disorder_id');
		$this->dropColumn('common_ophthalmic_disorder_version','alternate_disorder_id');
	}

}