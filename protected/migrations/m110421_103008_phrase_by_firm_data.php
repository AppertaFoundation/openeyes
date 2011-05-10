<?php

class m110421_103008_phrase_by_firm_data extends CDbMigration
{
	public function up()
	{
		# populate section_by_firm - id, name
		$sections = array('Introduction','Findings','Diagnosis','Management','Drugs','Outcome');
		$section_type_letter = $this->dbConnection->createCommand()->select()->from('section_type')->where('name=:name', array(':name'=> 'Letter'))->queryRow();
		$section_type_exam = $this->dbConnection->createCommand()->select()->from('section_type')->where('name=:name', array(':name'=> 'Exam'))->queryRow();

		foreach ($sections as $section) {
			$this->insert('section', array(
				'name' => $section,
				'section_type_id' => $section_type_letter['id']
			));
		}

		$section_letter_introduction = $this->dbConnection->createCommand()->select()->from('section')->where('name=:name AND section_type_id=:section_type_id', array(':name'=> 'Introduction', ':section_type_id'=> $section_type_letter['id']))->queryRow();
		$section_letter_drugs = $this->dbConnection->createCommand()->select()->from('section')->where('name=:name AND section_type_id=:section_type_id', array(':name'=> 'Drugs', ':section_type_id'=> $section_type_letter['id']))->queryRow();

		$this->insert('phrase_by_firm', array(
			'name' => 'Referral',
			'phrase' => 'Thanks for referrring this [age] old [sub] who I saw today',
			'section_id' => $section_letter_introduction['id'],
			'display_order' => 1,
			'firm_id' => 1,
		));
		$this->insert('phrase_by_firm', array(
			'name' => 'Emergency',
			'phrase' => 'I saw this [age] old [sub] as an emergency today',
			'section_id' => $section_letter_introduction['id'],
			'display_order' => 2,
			'firm_id' => 1,
		));
		$this->insert('phrase_by_firm', array(
			'name' => 'Diagnosis',
			'phrase' => 'His principal diagnosis is conjunctivitis',
			'section_id' => $section_letter_drugs['id'],
			'display_order' => 3,
			'firm_id' => 1,
		));
	}

	public function down()
	{
		$this->delete('section', 'section_type_id=:section_type_id', array(':section_type_id' => $section_type_letter['id']));
		$this->truncateTable('phrase_by_firm');
	}
}
