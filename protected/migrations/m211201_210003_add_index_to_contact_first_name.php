<?php

class m211201_210003_add_index_to_contact_first_name extends CDbMigration
{
	public function up()
	{
		// Speeding up first name searches with an Index
		$this->execute("CREATE INDEX IF NOT EXISTS contact_first_name_key ON contact(first_name);");
	}

	public function down()
	{
		$this->execute("DROP INDEX IF EXISTS contact_first_name_key ON contact;");
	}

}
