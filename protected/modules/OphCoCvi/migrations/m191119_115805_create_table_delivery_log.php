<?php

class m191119_115805_create_table_delivery_log extends CDbMigration
{
	public function up()
	{
	    $this->createTable("ophcocvi_delivery_log", array(
	        "id" => "pk",
            "event_id" => "INT(10) UNSIGNED NOT NULL",
            "delivery_to" => "VARCHAR(8)",
            "attempted_at" => "DATETIME",
            "status" => "VARCHAR(16) NULL",
            "error_report" => "TEXT NULL"
        ));

	    $this->addForeignKey("fk_ophcocvi_delivery_log_eid", "ophcocvi_delivery_log", "event_id", "event", "id");
	}

	public function down()
	{
        $this->dropForeignKey("fk_ophcocvi_delivery_log_eid", "ophcocvi_delivery_log");
        $this->dropTable("ophcocvi_delivery_log");
	}
}