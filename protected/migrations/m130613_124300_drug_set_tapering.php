<?php

class m130613_124300_drug_set_tapering extends CDbMigration
{
	public function up()
	{
		$this->addColumn('drug_set_item', 'dose', 'varchar(40)');

		$this->alterColumn('drug_set_item', 'default_frequency_id', 'int(10) unsigned');
		$this->dropForeignKey('drug_set_item_default_frequency_id_fk', 'drug_set_item');
		$this->renameColumn('drug_set_item', 'default_frequency_id', 'frequency_id');
		$this->addForeignKey('drug_set_item_frequency_id_fk', 'drug_set_item', 'frequency_id', 'drug_frequency', 'id');

		$this->alterColumn('drug_set_item', 'default_duration_id', 'int(10) unsigned');
		$this->dropForeignKey('drug_set_item_default_duration_id_fk', 'drug_set_item');
		$this->renameColumn('drug_set_item', 'default_duration_id', 'duration_id');
		$this->addForeignKey('drug_set_item_duration_id_fk', 'drug_set_item', 'duration_id', 'drug_duration', 'id');

		$this->createTable('drug_set_item_taper', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'item_id' => 'int(10) unsigned NOT NULL',
				'dose' => 'varchar(40)',
				'frequency_id' => 'int(10) unsigned',
				'duration_id' => 'int(10) unsigned',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `drug_set_item_taper_f_fk` (`frequency_id`)',
				'KEY `drug_set_item_taper_d_fk` (`duration_id`)',
				'KEY `drug_set_item_taper_lmui_fk` (`last_modified_user_id`)',
				'KEY `drug_set_item_taper_cui_fk` (`created_user_id`)',
				'CONSTRAINT `drug_set_item_taper_f_fk` FOREIGN KEY (`frequency_id`) REFERENCES `drug_frequency` (`id`)',
				'CONSTRAINT `drug_set_item_taper_d_fk` FOREIGN KEY (`duration_id`) REFERENCES `drug_duration` (`id`)',
				'CONSTRAINT `drug_set_item_taper_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `drug_set_item_taper_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	}

	public function down()
	{
		$this->dropTable('drug_set_item_taper');

		$this->dropForeignKey('drug_set_item_frequency_id_fk', 'drug_set_item');
		$this->renameColumn('drug_set_item', 'frequency_id', 'default_frequency_id');
		$this->addForeignKey('drug_set_item_default_frequency_id_fk', 'drug_set_item', 'default_frequency_id', 'drug_frequency', 'id');
		$this->alterColumn('drug_set_item', 'default_frequency_id', 'int(10) unsigned not null');

		$this->dropForeignKey('drug_set_item_duration_id_fk', 'drug_set_item');
		$this->renameColumn('drug_set_item', 'duration_id', 'default_duration_id');
		$this->addForeignKey('drug_set_item_default_duration_id_fk', 'drug_set_item', 'default_duration_id', 'drug_duration', 'id');
		$this->alterColumn('drug_set_item', 'default_duration_id', 'int(10) unsigned not null');

		$this->dropColumn('drug_set_item','dose');
	}

}
