<?php

class m111124_135356_cancelled_operation_reason_text_field extends CDbMigration
{
	public function up()
	{
		$this->addColumn('cancelled_operation', 'cancellation_comment', 'varchar(200) COLLATE utf8_bin NOT NULL');
	}

	public function down()
	{
		$this->dropColumn('cancelled_operation', 'cancellation_comment');
	}
}
