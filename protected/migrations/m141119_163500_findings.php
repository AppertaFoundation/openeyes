<?php

class m141119_163500_findings extends OEMigration
{
	public function up()
	{
		$this->createOETable('finding', array(
			'id' => 'pk',
			'name' => 'varchar(255)',
			'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'active' => 'tinyint(1) unsigned not null DEFAULT 1'
		), true);
		$this->createIndex('finding_unique_name', 'finding', 'name', true);
		$this->createOETable('findings_subspec_assignment', array(
			'id' => 'pk',
			'finding_id' => 'int(11) NOT NULL',
			'subspecialty_id' => 'int(10) unsigned NOT NULL',
			'CONSTRAINT `findings_subspec_f_id_fk` FOREIGN KEY (`finding_id`) REFERENCES `finding` (`id`)',
			'CONSTRAINT `findings_subspec_s_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)',
		), true);
	}

	public function down()
	{
		$this->dropOETable('findings_subspec_assignment', true);
		$this->dropOETable('finding', true);
	}

}