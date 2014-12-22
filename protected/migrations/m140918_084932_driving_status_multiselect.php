<?php

class m140918_084932_driving_status_multiselect extends OEMigration
{
	public function up()
	{
		$this->createTable('socialhistory_driving_status_assignment', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'socialhistory_id' => 'int(10) unsigned not null',
				'driving_status_id' => 'int(10) unsigned not null',
				'display_order' => 'tinyint(1) unsigned not null',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `socialhistory_driving_status_assignment_lmui_fk` (`last_modified_user_id`)',
				'KEY `socialhistory_driving_status_assignment_cui_fk` (`created_user_id`)',
				'KEY `socialhistory_driving_status_assignment_shi_fk` (`socialhistory_id`)',
				'KEY `socialhistory_driving_status_assignment_dsi_fk` (`driving_status_id`)',
				'CONSTRAINT `socialhistory_driving_status_assignment_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `socialhistory_driving_status_assignment_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `socialhistory_driving_status_assignment_shi_fk` FOREIGN KEY (`socialhistory_id`) REFERENCES `socialhistory` (`id`)',
				'CONSTRAINT `socialhistory_driving_status_assignment_dsi_fk` FOREIGN KEY (`driving_status_id`) REFERENCES `socialhistory_driving_status` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$this->versionExistingTable('socialhistory_driving_status_assignment');

		foreach ($this->dbConnection->createCommand()->select("*")->from("socialhistory")->order("id asc")->queryAll() as $row) {
			if ($row['driving_status_id'] !== null) {
				$this->insert('socialhistory_driving_status_assignment',array(
					'id' => $row['id'],
					'socialhistory_id' => $row['id'],
					'driving_status_id' => $row['driving_status_id'],
					'last_modified_user_id' => $row['last_modified_user_id'],
					'last_modified_date' => $row['last_modified_date'],
					'created_user_id' => $row['created_user_id'],
					'created_date' => $row['created_date'],
				));
			}

			foreach ($this->dbConnection->createCommand()->select("*")->from("socialhistory_version")->where("id = {$row['id']}")->order("version_id asc")->queryAll() as $v) {
				if ($v['driving_status_id'] !== null) {
					$this->insert('socialhistory_driving_status_assignment_version',array(
						'id' => $row['id'],
						'version_id' => $v['version_id'],
						'version_date' => $v['version_date'],
						'socialhistory_id' => $row['id'],
						'driving_status_id' => $v['driving_status_id'],
						'last_modified_user_id' => $v['last_modified_user_id'],
						'last_modified_date' => $v['last_modified_date'],
						'created_user_id' => $v['created_user_id'],
						'created_date' => $v['created_date'],
					));
				}
			}
		}

		$this->dropForeignKey('socialhistory_driving_status_fk','socialhistory');
		$this->dropColumn('socialhistory','driving_status_id');
		$this->dropColumn('socialhistory_version','driving_status_id');
	}

	public function down()
	{
		$this->addColumn('socialhistory','driving_status_id','int(10) unsigned null');
		$this->addColumn('socialhistory_version','driving_status_id','int(10) unsigned null');

		echo "Warning: down migration may be lossy as we're transitioning back to a single driving status.\n";

		$socialhistory_ids = array();

		foreach ($this->dbConnection->createCommand()->select("*")->from("socialhistory_driving_status_assignment")->order("id asc")->queryAll() as $row) {
			if (!in_array($row['socialhistory_id'],$socialhistory_ids)) {
				$socialhistory_ids[] = $row['socialhistory_id'];

				$this->update('socialhistory',array('driving_status_id' => $row['driving_status_id']),"id = {$row['socialhistory_id']}");

				foreach ($this->dbConnection->createCommand()->select("*")->from("socialhistory_driving_status_assignment_version")->where("id={$row['id']}")->order("version_id asc")->queryAll() as $v) {
					$this->update('socialhistory_version',array(
							'driving_status_id' => $v['driving_status_id'],
						),
						"version_id = {$v['version_id']}"
					);
				}
			}
		}

		$this->addForeignKey('socialhistory_driving_status_fk','socialhistory','driving_status_id','socialhistory_driving_status','id');

		$this->dropTable('socialhistory_driving_status_assignment_version');
		$this->dropTable('socialhistory_driving_status_assignment');
	}
}
