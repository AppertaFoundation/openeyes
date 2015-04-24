<?php

class m150422_140607_medication_common extends OEMigration
{
	public function up()
	{
		$this->createOETable(
			'medication_common',
			array(
				'id' => 'pk',
				'medication_id' => 'int(11) not null',
				'CONSTRAINT `medication_common_medication_drug_id_fk` FOREIGN KEY (`medication_id`) REFERENCES `medication_drug` (`id`)',
			),
			true
		);
	}

	public function down()
	{
		$this->dropOETable('medication_common',true);
	}
}