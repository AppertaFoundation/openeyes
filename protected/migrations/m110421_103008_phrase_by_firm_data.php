<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk   info@openeyes.org.uk
--
*/

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
		$section_type_letter = $this->dbConnection->createCommand()->select()->from('section_type')->where('name=:name', array(':name'=> 'Letter'))->queryRow();
		$this->truncateTable('phrase_by_firm');
		$this->delete('section', 'section_type_id=:section_type_id', array(':section_type_id' => $section_type_letter['id']));
	}
}
