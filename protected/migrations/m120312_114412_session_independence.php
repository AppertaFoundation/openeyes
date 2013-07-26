<?php

class m120312_114412_session_independence extends CDbMigration
{
	public function up()
	{
		// Copy theatre relationship to session
		$this->addColumn('session','theatre_id','int(10) unsigned NOT NULL');
		$this->execute('UPDATE `session` s, `sequence` q SET s.theatre_id = q.theatre_id WHERE s.sequence_id = q.id');

		// Copy firm relationship to session
		$this->createTable('session_firm_assignment', array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'session_id' => 'int(10) unsigned NOT NULL',
			'firm_id' => 'int(10) unsigned NOT NULL',
			'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'created_date' => 'datetime NOT NULL',
			'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
			'last_modified_date' => 'datetime NOT NULL',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `session_firm_assignment_session_id` (`session_id`)',
		), 'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
		$this->addForeignKey('session_firm_assignment_session','session_firm_assignment','session_id','session','id');
		$this->addForeignKey('session_firm_assignment_firm','session_firm_assignment','firm_id','firm','id');
		$this->execute('
			INSERT INTO session_firm_assignment (session_id, firm_id)
			SELECT s.id, sqa.firm_id
			FROM `sequence_firm_assignment` sqa
			JOIN `sequence` q ON q.id = sqa.sequence_id
			JOIN `session` s ON s.sequence_id = q.id
		');

		// Enforce many to 1 between sequence and firm
		$this->createIndex('sequence_firm_assignment_sequence_id','sequence_firm_assignment','sequence_id', true);

	}

	public function down()
	{
		$this->dropColumn('session','theatre_id');
		$this->dropTable('session_firm_assignment');
		$this->dropIndex('sequence_firm_assignment_sequence_id','sequence_firm_assignment');
	}

	public function safeUp()
	{
		$this->up();
	}

	public function safeDown()
	{
		$this->down();
	}

}
