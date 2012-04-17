<?php

class m120411_143652_site_subspecialty_anaesthetics extends CDbMigration
{
	public function up()
	{
		$this->createTable('site_subspecialty_anaesthetic_agent',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'site_id' => 'int(10) unsigned NOT NULL',
				'subspecialty_id' => 'int(10) unsigned NOT NULL',
				'anaesthetic_agent_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `site_subspecialty_anaesthetic_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `site_subspecialty_anaesthetic_created_user_id_fk` (`created_user_id`)',
				'KEY `site_subspecialty_anaesthetic_site_id` (`site_id`)',
				'KEY `site_subspecialty_anaesthetic_subspecialty_id` (`subspecialty_id`)',
				'KEY `site_subspecialty_anaesthetic_anaesthetic_agent_id` (`anaesthetic_agent_id`)',
				'CONSTRAINT `site_subspecialty_anaesthetic_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `site_subspecialty_anaesthetic_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `site_subspecialty_anaesthetic_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `site_subspecialty_anaesthetic_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)',
				'CONSTRAINT `site_subspecialty_anaesthetic_anaesthetic_agent_id_fk` FOREIGN KEY (`anaesthetic_agent_id`) REFERENCES `anaesthetic_agent` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		if ($subspecialty = $this->dbConnection->createCommand()->select('id')->from('subspecialty')->where('id=:id',array(':id'=>4))->queryRow()) {
			$this->insert('site_subspecialty_anaesthetic_agent',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'anaesthetic_agent_id'=>1));
			$this->insert('site_subspecialty_anaesthetic_agent',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'anaesthetic_agent_id'=>2));
			$this->insert('site_subspecialty_anaesthetic_agent',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'anaesthetic_agent_id'=>3));
			$this->insert('site_subspecialty_anaesthetic_agent',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'anaesthetic_agent_id'=>4));
			$this->insert('site_subspecialty_anaesthetic_agent',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'anaesthetic_agent_id'=>5));
			$this->insert('site_subspecialty_anaesthetic_agent',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'anaesthetic_agent_id'=>6));
		}

		$this->createTable('site_subspecialty_anaesthetic_agent_default',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'site_id' => 'int(10) unsigned NOT NULL',
				'subspecialty_id' => 'int(10) unsigned NOT NULL',
				'anaesthetic_agent_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `site_subspecialty_anaesthetic_def_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `site_subspecialty_anaesthetic_def_created_user_id_fk` (`created_user_id`)',
				'KEY `site_subspecialty_anaesthetic_def_site_id` (`site_id`)',
				'KEY `site_subspecialty_anaesthetic_def_subspecialty_id` (`subspecialty_id`)',
				'KEY `site_subspecialty_anaesthetic_def_anaesthetic_agent_id` (`anaesthetic_agent_id`)',
				'CONSTRAINT `site_subspecialty_anaesthetic_def_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `site_subspecialty_anaesthetic_def_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `site_subspecialty_anaesthetic_def_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `site_subspecialty_anaesthetic_def_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)',
				'CONSTRAINT `site_subspecialty_anaesthetic_def_anaesthetic_agent_id_fk` FOREIGN KEY (`anaesthetic_agent_id`) REFERENCES `anaesthetic_agent` (`id`)',
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		if ($subspecialty) {
			$this->insert('site_subspecialty_anaesthetic_agent_default',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'anaesthetic_agent_id'=>1));
			$this->insert('site_subspecialty_anaesthetic_agent_default',array('site_id'=>1,'subspecialty_id'=>$subspecialty['id'],'anaesthetic_agent_id'=>2));
		}
	}

	public function down()
	{
		$this->dropTable('site_subspecialty_anaesthetic_agent_default');
		$this->dropTable('site_subspecialty_anaesthetic_agent');
	}
}
