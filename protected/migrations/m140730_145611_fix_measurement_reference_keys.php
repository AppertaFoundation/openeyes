<?php

class m140730_145611_fix_measurement_reference_keys extends OEMigration
{
	public function safeUp()
	{
		$this->dropForeignKey('measurement_reference_episode_id_fk', 'measurement_reference');
		$this->dropForeignKey('measurement_reference_event_id_fk', 'measurement_reference');

		$this->dropIndex('measurement_reference_event_id_fk', 'measurement_reference');

		$this->createIndex('measurement_reference_episode_id_unique', 'measurement_reference', 'episode_id,patient_measurement_id', true);
		$this->createIndex('measurement_reference_event_id_unique', 'measurement_reference', 'event_id,patient_measurement_id', true);

		$this->addForeignKey('measurement_reference_episode_id_fk', 'measurement_reference', 'episode_id', 'episode', 'id');
		$this->addForeignKey('measurement_reference_event_id_fk', 'measurement_reference', 'event_id', 'event', 'id');
	}

	public function safeDown()
	{
		echo "m140730_145611_fix_measurement_reference_keys does not support migration down.\n";
		return false;
	}
}