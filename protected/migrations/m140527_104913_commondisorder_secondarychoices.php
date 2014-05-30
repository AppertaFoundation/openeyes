<?php

class m140527_104913_commondisorder_secondarychoices extends OEMigration
{
	public function up()
	{
		$this->createOETable('secondaryto_common_oph_disorder', array(
						'id' => 'pk',
						'disorder_id' => 'int(10) unsigned NOT NULL',
						'parent_id' => 'int(10) unsigned NOT NULL',
				), true);

		$this->addForeignKey('secondaryto_common_oph_disorder_did_fk',
				'secondaryto_common_oph_disorder', 'disorder_id', 'disorder', 'id');
		$this->addForeignKey('secondaryto_common_oph_disorder_pid_fk',
				'secondaryto_common_oph_disorder', 'parent_id', 'common_ophthalmic_disorder', 'id');

	}

	public function down()
	{
		$this->dropOETable('secondaryto_common_oph_disorder', true);
	}

}