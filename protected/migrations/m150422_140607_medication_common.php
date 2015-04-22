<?php

class m150422_140607_medication_common extends OEMigration
{
	public function up()
	{
		$this->createOETable(
			'medication_common',
			array(
				'id' => 'pk',
				'medication_id' => 'int(10) unsigned not null',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'CONSTRAINT `medication_common_l_m_u_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `medication_common_c_u_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `medication_common_medication_id_fk` FOREIGN KEY (`medication_id`) REFERENCES `medication` (`id`)',
			),
			true
		);
	}

	public function down()
	{
		$this->dropOETable('medication_common_version',true);
		$this->dropOETable('medication_common',true);
	}
}