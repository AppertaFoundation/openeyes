<?php

class m141120_164600_findings_in_common_oph_disorder extends CDbMigration
{
	public function up()
	{
		$this->addColumn('common_ophthalmic_disorder', 'finding_id', 'int');
		$this->addColumn('common_ophthalmic_disorder_version', 'finding_id', 'int');
		$this->addForeignKey('common_ophthalmic_disorder_finding_fk','common_ophthalmic_disorder','finding_id','finding','id');
	}

	public function down()
	{
		$this->dropColumn('common_ophthalmic_disorder_version', 'finding_id');
		$this->dropColumn('common_ophthalmic_disorder', 'finding_id');
	}
}