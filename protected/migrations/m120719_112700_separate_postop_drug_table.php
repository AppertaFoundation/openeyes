<?php

class m120719_112700_separate_postop_drug_table extends CDbMigration {

	public function up() {
		$this->createTable('postop_drug',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `postop_drug_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `postop_drug_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `postop_drug_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `postop_drug_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
		),
				'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		// Remove old foreign key to prevent ref int issues
		$this->dropForeignKey('site_subspecialty_drug_drug_id_fk', 'site_subspecialty_drug');

		// Move any existing drugs over to new table
		$drugs = $this->dbConnection->createCommand()
		->selectDistinct('drug.id, drug.name')
		->from('drug')
		->join('site_subspecialty_drug','site_subspecialty_drug.drug_id = drug.id')
		->query();
		if($drugs) {

			// Use datestamp to avoid colliding IDs
			$datestamp = date('Y-m-d H:i:s');
			
			foreach($drugs as $drug) {
				$this->insert('postop_drug', array('name' => $drug['name']));
				$new_drug_id = $this->dbConnection->createCommand()
				->select('id')
				->from('postop_drug')
				->where('name = :drug_name')
				->queryScalar(array(':drug_name' => $drug['name']));
				$this->update('site_subspecialty_drug',
						array('drug_id' => $new_drug_id, 'last_modified_date' => $datestamp),
						'drug_id = :drug_id AND last_modified_date != :datestamp',
						array(':drug_id' => $drug['id'], ':datestamp' => $datestamp)
				);
			}
		}

		// Add new foreign key
		$this->addForeignKey('site_subspecialty_drug_drug_id_fk', 'site_subspecialty_drug', 'drug_id', 'postop_drug', 'id');

	}

	public function down() {
		$this->dropForeignKey('site_subspecialty_drug_drug_id_fk', 'site_subspecialty_drug');
		$drugs = $this->dbConnection->createCommand()
		->select('id, name')
		->from('postop_drug')
		->query();
		if($drugs) {
			$datestamp = date('Y-m-d H:i:s');
			foreach($drugs as $drug) {
				$this->insert('drug', array('name' => $drug['name']));
				$new_drug_id = $this->dbConnection->createCommand()
				->select('id')
				->from('drug')
				->where('name = :drug_name')
				->queryScalar(array(':drug_name' => $drug['name']));
				$this->update('site_subspecialty_drug',
						array('drug_id' => $new_drug_id, 'last_modified_date' => $datestamp),
						'drug_id = :drug_id AND last_modified_date != :datestamp',
						array(':drug_id' => $drug['id'], ':datestamp' => $datestamp)
				);
			}
		}
		$this->addForeignKey('site_subspecialty_drug_drug_id_fk', 'site_subspecialty_drug', 'drug_id', 'drug', 'id');
		$this->dropTable('postop_drug');
	}

}
