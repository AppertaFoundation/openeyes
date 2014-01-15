<?php

class m131211_094237_custom_episode_summaries extends OEMigration
{
	public function up()
	{
		$this->createOETable(
			'episode_summary_item',
			array(
				'id' => 'int unsigned not null auto_increment primary key',
				'event_type_id' => 'int unsigned not null',
				'name' => 'varchar(85) not null',
				'unique (name, event_type_id)',
				'constraint episode_summary_item_etid_fk foreign key (event_type_id) references event_type (id)',
			)
		);

		$this->createOETable(
			'episode_summary',
			array(
				'id' => 'int unsigned not null auto_increment primary key',
				'item_id' => 'int unsigned not null',
				'subspecialty_id' => 'int unsigned default null',
				'display_order' => 'int unsigned not null',
				'unique (item_id, subspecialty_id)',
				'constraint episode_summary_iid_fk foreign key (item_id) references episode_summary_item (id)',
				'constraint episode_summarry_ssid_fk foreign key (subspecialty_id) references subspecialty (id)',
			)
		);
	}

	public function down()
	{
		$this->dropTable('episode_summary');
		$this->dropTable('episode_summary_item');
	}
}
