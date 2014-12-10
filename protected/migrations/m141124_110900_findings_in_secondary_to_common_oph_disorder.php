<?php

class m141124_110900_findings_in_secondary_to_common_oph_disorder extends CDbMigration
{
	public function up()
	{
		$this->addColumn('secondaryto_common_oph_disorder', 'finding_id', 'int');
		$this->addColumn('secondaryto_common_oph_disorder_version', 'finding_id', 'int');
		$this->addForeignKey('secondaryto_common_oph_disorder_finding_fk','secondaryto_common_oph_disorder','finding_id','finding','id');
		$this->alterColumn('secondaryto_common_oph_disorder', 'disorder_id', 'int(10) unsigned');
	}

	public function down()
	{
		$this->alterColumn('secondaryto_common_oph_disorder', 'disorder_id', 'int(10) unsigned NOT NULL');
		$this->dropColumn('secondaryto_common_oph_disorder_version', 'finding_id');
		$this->dropForeignKey('secondaryto_common_oph_disorder_finding_fk','secondaryto_common_oph_disorder');
		$this->dropColumn('secondaryto_common_oph_disorder', 'finding_id');
	}
}