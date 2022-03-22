<?php

class m211201_192945_add_index_to_event_version extends CDbMigration
{
	public function up()
	{
		// Version tables are left without an index on id as PK is not id, creating one speeds up click on a patient (events lookup)
		$this->execute("CREATE INDEX IF NOT EXISTS idx_event_version_id ON event_version(id);");
	}

	public function down()
	{
		$this->execute("DROP INDEX IF EXISTS idx_event_version_id ON event_version;");
	}
}
