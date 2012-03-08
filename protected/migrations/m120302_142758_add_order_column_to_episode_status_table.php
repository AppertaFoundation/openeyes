<?php

class m120302_142758_add_order_column_to_episode_status_table extends CDbMigration
{
	public function up()
	{
		$this->addColumn('episode_status','order','int(10) unsigned NOT NULL DEFAULT 0');

		for ($i=1; $i<=6; $i++) {
			$this->update('episode_status',array('order' => $i), "id = $i");
		}
	}

	public function down()
	{
		$this->dropColumn('episode_status','order');
	}
}
