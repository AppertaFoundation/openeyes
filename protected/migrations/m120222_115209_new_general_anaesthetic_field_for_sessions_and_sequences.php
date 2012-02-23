<?php

class m120222_115209_new_general_anaesthetic_field_for_sessions_and_sequences extends CDbMigration
{
	public function up()
	{
		$this->addColumn('sequence','general_anaesthetic','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->addColumn('session','general_anaesthetic','tinyint(1) unsigned NOT NULL DEFAULT 0');
		$this->update('sequence',array('general_anaesthetic' => 1), 'anaesthetist = 1');
		$this->update('session',array('general_anaesthetic' => 1), 'anaesthetist = 1');

		$sequences = array();

		// Anaesthetists at St Anns, Mile End and Potters Bar cannot do general anaesthetic
		foreach ($this->dbConnection->createCommand()
			->select('sequence.id')
			->from('sequence')
			->join('theatre','theatre.id = sequence.theatre_id')
			->join('site','site.id = theatre.site_id')
			->where("site.short_name in ('St Ann''s','Mile End','Potters Bar')")
			->queryAll() as $row) {
			$sequences[] = $row['id'];
		}

		if (!empty($sequences)) {
			$this->update('sequence',array('general_anaesthetic' => 0),'id in ('.implode(',',$sequences).')');
			$this->update('session',array('general_anaesthetic' => 0),'sequence_id in ('.implode(',',$sequences).')');
		}
	}

	public function down()
	{
		$this->dropColumn('sequence','general_anaesthetic');
		$this->dropColumn('session','general_anaesthetic');
	}
}
