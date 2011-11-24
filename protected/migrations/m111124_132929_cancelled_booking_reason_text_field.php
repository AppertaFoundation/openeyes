<?php

class m111124_132929_cancelled_booking_reason_text_field extends CDbMigration
{
	public function up()
	{
		$this->addColumn('cancelled_booking', 'cancellation_comment', 'varchar(200) COLLATE utf8_bin NOT NULL');
	}

	public function down()
	{
		$this->dropColumn('cancelled_booking', 'cancellation_comment');
	}
}
