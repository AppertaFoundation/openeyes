<?php

class m140515_160812_medication_history extends OEMigration
{
	public function safeUp()
	{
		$this->createOETable(
			"medication_stop_reason",
			array(
				"id" => "pk",
				"name" => "string not null",
				"display_order" => "integer not null",
				"active" => "boolean not null default true",
			),
			true
		);

		$this->addColumn("medication", "dose", "string");
		$this->addColumn("medication_version", "dose", "string");
		$this->addColumn("medication", "stop_reason_id", "integer");
		$this->addColumn("medication_version", "stop_reason_id", "integer");

		$this->addForeignKey("medication_stop_reason_id_fk", "medication", "stop_reason_id", "medication_stop_reason", "id");

		$this->initialiseData(__DIR__);
	}

	public function safeDown()
	{
		$this->dropForeignKey("medication_stop_reason_id_fk", "medication");

		$this->dropColumn("medication", "dose");
		$this->dropColumn("medication", "stop_reason_id");

		$this->dropOETable("medication_stop_reason", true);
	}
}
