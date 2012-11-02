<?php

class m121002_121025_new_multiple_diagnoses_table extends CDbMigration
{
	public function up()
	{
		$this->createTable('secondary_diagnosis',array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'disorder_id' => 'int(10) unsigned NOT NULL',
				'eye_id' => 'int(10) unsigned NULL',
				'patient_id' => 'int(10) unsigned NOT NULL',
				'date' => 'varchar(10) NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT \'1\'',
				'created_date' => 'datetime NOT NULL DEFAULT \'1900-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `secondary_diagnosis_disorder_id_fk` (`disorder_id`)',
				'KEY `secondary_diagnosis_eye_id_fk` (`eye_id`)',
				'KEY `secondary_diagnosis_patient_id_fk` (`patient_id`)',
				'KEY `secondary_diagnosis_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `secondary_diagnosis_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `secondary_diagnosis_disorder_id_fk` FOREIGN KEY (`disorder_id`) REFERENCES `disorder` (`id`)',
				'CONSTRAINT `secondary_diagnosis_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
				'CONSTRAINT `secondary_diagnosis_patient_id_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`id`)',
				'CONSTRAINT `secondary_diagnosis_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `secondary_diagnosis_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			),
			'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin'
		);

		$this->addColumn('episode','eye_id','int(10) unsigned NULL');
		$this->createIndex('episode_eye_id_fk','episode','eye_id');
		$this->addForeignKey('episode_eye_id_fk','episode','eye_id','eye','id');

		$this->addColumn('episode','disorder_id','int(10) unsigned NULL');
		$this->createIndex('episode_disorder_id_fk','episode','disorder_id');
		$this->addForeignKey('episode_disorder_id_fk','episode','disorder_id','disorder','id');

		echo "Populating principal eyes and diagnoses for episodes ... ";

		foreach (Yii::app()->db->createCommand("select * from episode")->queryAll() as $episode) {
			$diagnosis = null;

			$criteria = new CDbCriteria();
			$criteria->join = 'JOIN event ev ON t.event_id = ev.id';
			$criteria->addCondition('ev.episode_id = :episode_id');
			$criteria->params = array(':episode_id' => $episode['id']);
			$criteria->order = 't.created_date DESC, t.id DESC';
			$element = ElementDiagnosis::model()->find($criteria);
			if ($element && (!$diagnosis || strtotime($element->created_date) > strtotime($diagnosis->created_date))) {
				Yii::app()->db->createCommand("update episode set disorder_id = $element->disorder_id where id = {$episode['id']}")->query();
				Yii::app()->db->createCommand("update episode set eye_id = $element->eye_id where id = {$episode['id']}")->query();
			}
		}

		echo "done\n";
	}

	public function down()
	{
		$this->dropForeignKey('episode_disorder_id_fk','episode');
		$this->dropIndex('episode_disorder_id_fk','episode');
		$this->dropColumn('episode','disorder_id');

		$this->dropForeignKey('episode_eye_id_fk','episode');
		$this->dropIndex('episode_eye_id_fk','episode');
		$this->dropColumn('episode','eye_id');

		$this->dropTable('secondary_diagnosis');
	}
}
