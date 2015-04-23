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
				'CONSTRAINT `medication_common_medication_id_fk` FOREIGN KEY (`medication_id`) REFERENCES `medication` (`id`)',
			),
			true
		);
	}

	public function down()
	{
		$this->dropOETable('medication_common',true);
	}
}