<?php

class m191119_093655_add_la_email_to_demographics extends CDbMigration
{
	public function up()
	{
	    $this->addColumn("et_ophcocvi_demographics", "la_email", "VARCHAR(255) NULL");
	    $this->addColumn("et_ophcocvi_demographics_version", "la_email", "VARCHAR(255) NULL");
	}

	public function down()
	{
		$this->dropColumn("et_ophcocvi_demographics", "la_email");
		$this->dropColumn("et_ophcocvi_demographics_version", "la_email");
	}
}