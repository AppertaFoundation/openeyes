<?php

class m191119_101121_add_eventinfo_fields extends CDbMigration
{
	public function up()
	{
	    foreach (["et_ophcocvi_eventinfo", "et_ophcocvi_eventinfo_version"] as $table) {
            $this->addColumn($table, "gp_delivery", "BOOLEAN NOT NULL");
            $this->addColumn($table, "gp_delivery_status", "VARCHAR(16) NULL");

            $this->addColumn($table, "la_delivery", "BOOLEAN NOT NULL");
            $this->addColumn($table, "la_delivery_status", "VARCHAR(16) NULL");

            $this->addColumn($table, "rco_delivery", "BOOLEAN NOT NULL");
            $this->addColumn($table, "rco_delivery_status", "VARCHAR(16) NULL");
        }
	}

	public function down()
	{
        foreach (["et_ophcocvi_eventinfo", "et_ophcocvi_eventinfo_version"] as $table) {
            $this->dropColumn($table, "gp_delivery");
            $this->dropColumn($table, "gp_delivery_status");

            $this->dropColumn($table, "la_delivery");
            $this->dropColumn($table, "la_delivery_status");

            $this->dropColumn($table, "rco_delivery");
            $this->dropColumn($table, "rco_delivery_status");
        }
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}