<?php

class m130508_103925_support_services extends CDbMigration
{
	public function up()
	{
		$this->createTable('support_service', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'display_order' => 'int(10) unsigned NOT NULL DEFAULT 0',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `support_service_lmui_fk` (`last_modified_user_id`)',
				'KEY `support_service_cui_fk` (`created_user_id`)',
				'CONSTRAINT `support_service_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `support_service_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('support_service',array('id'=>1,'name'=>'Optometry','display_order'=>1));
		$this->insert('support_service',array('id'=>2,'name'=>'Orthoptics','display_order'=>2));
		$this->insert('support_service',array('id'=>3,'name'=>'EDD','display_order'=>3));
		$this->insert('support_service',array('id'=>4,'name'=>'Ultrasound','display_order'=>4));

		$this->addColumn('episode','support_service','tinyint(1) unsigned NOT NULL DEFAULT 0');

		$ssas = array();
		foreach (array('OPT','ORT','EDD') as $ref_spec) {
			foreach (Yii::app()->db->createCommand()
				->select("ssa.id")
				->from("subspecialty s")
				->join("service_subspecialty_assignment ssa","ssa.subspecialty_id = s.id")
				->where("ref_spec = ?",array($ref_spec))
				->queryAll() as $row) {
				$ssas[] = $row['id'];
			}
		}

		$firm_ids = array();
		foreach (Yii::app()->db->createCommand()
			->select("f.id")
			->from("firm f")
			->where("service_subspecialty_assignment_id in (".implode(',',$ssas).")")
			->queryAll() as $firm) {
			$firm_ids[] = $firm['id'];
		}

		echo "Migrating support service events ...\n";

		foreach ($firm_ids as $firm_id) {
			foreach (Yii::app()->db->createCommand()
				->select("episode.id, episode.patient_id")
				->from("episode")
				->where("firm_id in (".implode(',',$firm_ids).")")
				->queryAll() as $episode) {

				$supportEpisode = $this->getSupportEpisode($episode['patient_id'], $firm_ids);

				foreach (Yii::app()->db->createCommand()
					->select("event.id")
					->from("event")
					->where("episode_id=?",array($episode['id']))
					->queryAll() as $event) {

					$this->update('event',array('episode_id'=>$supportEpisode['id']),"id={$event['id']}");
				}

				$this->delete('audit',"episode_id={$episode['id']} and action='create' and target_type='episode'");
				$this->update('audit',array('episode_id'=>$supportEpisode['id']),"episode_id={$episode['id']}");
				$this->delete('episode',"id={$episode['id']}");
			}
		}
	}

	public function getSupportEpisode($patient_id, $firm_ids) {
		if ($episode = Yii::app()->db->createCommand()
			->select("episode.*")
			->from("episode")
			->where("patient_id = ? and support_service = ?",array($patient_id,1))
			->queryRow()) {
			return $episode;
		}

		// find the earliest episode for one of the old support service firms and use it as a template for the new episode
		$episodes = array();
		foreach (Yii::app()->db->createCommand()
			->select("episode.*")
			->from("episode")
			->where("patient_id = ? and firm_id in (".implode(',',$firm_ids).")",array($patient_id))
			->queryAll() as $episode) {
			$ts = strtotime($episode['start_date']);
			$episodes[$ts] = $episode;
		}

		ksort($episodes);
		$episode = array_shift($episodes);

		$this->insert('episode',array(
			'patient_id' => $patient_id,
			'firm_id' => null,
			'start_date' => $episode['start_date'],
			'end_date' => $episode['end_date'],
			'last_modified_user_id' => $episode['last_modified_user_id'],
			'last_modified_date' => $episode['last_modified_date'],
			'created_user_id' => $episode['created_user_id'],
			'created_date' => $episode['created_date'],
			'episode_status_id' => $episode['episode_status_id'],
			'legacy' => 0,
			'deleted' => $episode['deleted'],
			'eye_id' => $episode['eye_id'],
			'disorder_id' => $episode['disorder_id'],
			'support_service' => 1,
		));

		$episode_id = Yii::app()->db->createCommand()
			->select("max(id)")
			->from("episode")
			->queryScalar();

		$this->update('audit',array('episode_id'=>$episode_id),"episode_id={$episode['id']}");

		return Yii::app()->db->createCommand()
			->select("episode.*")
			->from("episode")
			->where("id=?",array($episode_id))
			->queryRow();
	}

	public function down()
	{
		$this->dropColumn('episode','support_service');
		$this->dropTable('support_service');
	}
}
