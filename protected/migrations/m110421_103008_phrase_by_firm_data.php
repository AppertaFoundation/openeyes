<?php

class m110421_103008_phrase_by_firm_data extends CDbMigration
{
	public function up()
	{
		# populate section_by_firm - id, name
		$sections = array('Introduction','Findings','Diagnosis','Management','Drugs','Outcome');

		foreach ($sections as $section) {
			$this->insert('section_by_firm', array(
				'name' => $section,
			));
		}

		$this->insert('phrase_by_firm', array(
			'name' => 'Referral',
			'phrase' => 'Thanks for referrring this [age] old [sub] who I saw today',
			'section_by_firm_id' => 1,
			'display_order' => 1,
			'firm_id' => 1,
		));
		$this->insert('phrase_by_firm', array(
			'name' => 'Emergency',
			'phrase' => 'I saw this [age] old [sub] as an emergency today',
			'section_by_firm_id' => 1,
			'display_order' => 2,
			'firm_id' => 1,
		));
		$this->insert('phrase_by_firm', array(
			'name' => 'Diagnosis',
			'phrase' => 'His principal diagnosis is conjunctivitis',
			'section_by_firm_id' => 2,
			'display_order' => 3,
			'firm_id' => 1,
		));
	}

	public function down()
	{
		$this->truncateTable('section_by_firm');
		$this->truncateTable('phrase_by_firm');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}
