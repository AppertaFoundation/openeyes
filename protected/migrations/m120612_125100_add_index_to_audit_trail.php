<?php

class m120612_125100_add_index_to_audit_trail extends CDbMigration
{
	public function up()
	{
		$this->createIndex('idx_audit_trail_stamp', 'tbl_audit_trail', 'stamp');
	}

	public function down()
	{
		$this->dropIndex('idx_audit_trail_stamp', 'tbl_audit_trail');
	}

}
