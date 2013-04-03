<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class PhraseInheritanceTest extends CDbTestCase
{
	public $fixtures = array(
		'sections' => 'Section',
		'sectionTypes' => 'SectionType',
		'phrases' => 'Phrase',
		'phrasesBySpecialty' => 'PhraseBySpecialty',
		'phrasesByFirm' => 'PhraseByFirm',
		'firms' => 'Firm',
	);

	public function testPhraseInheritance()
	{
		// create a Phrase with a new PhraseName for a particular section
		// make sure it's on the override list when creating a phrase by specialty for that section and a particular specialty  (PhraseBySpecialty::getOverrideableNames) [should be true]
		$sectionId = 1; $specialtyId = 1; $firmId = 1;

		$phraseName = new PhraseName;
		$phraseName->name = "Test name";
		$phraseName->save();
		$phraseName = PhraseName::model()->findByAttributes(array('name' => 'Test name'));

		// first make sure there is no match
		$overrideable = PhraseBySpecialty::model()->getOverrideableNames($sectionId, $specialtyId);

		$overrideMatches = false;
		foreach ($overrideable as $override) {
			if ($override->name == 'Test name') {
				$overrideMatches = true;
			}
		}
		$this->assertFalse($overrideMatches);

		// associate the name with a toplevel phrase
		$phrase = new Phrase;
		$phrase->phrase = 'Test phrase';
		$phrase->section_id = $sectionId;
		$phrase->phrase_name_id = $phraseName->id;
		$phrase->save();

		// test that it can now be overridden by specialty
		$overrideable = PhraseBySpecialty::model()->getOverrideableNames($sectionId, $specialtyId);

		$overrideMatches = false;
		foreach ($overrideable as $override) {
			if ($override->name == 'Test name') {
				$overrideMatches = true;
			}
		}
		$this->assertTrue($overrideMatches);

		// or by firm
		$overrideable = PhraseByFirm::model()->getOverrideableNames($sectionId, $firmId);

		$overrideMatches = false;
		foreach ($overrideable as $override) {
			if ($override->name == 'Test name') {
				$overrideMatches = true;
			}
		}
		$this->assertTrue($overrideMatches);

		// override the phrase by specialty
		$phrase = new PhraseBySpecialty;
		$phrase->phrase = 'Test phrase two';
		$phrase->section_id = $sectionId;
		$phrase->specialty_id = $specialtyId;
		$phrase->phrase_name_id = $phraseName->id;
		$phrase->save();

		// test that it can't now be overridden by specialty
		$overrideable = PhraseBySpecialty::model()->getOverrideableNames($sectionId, $specialtyId);

		$overrideMatches = false;
		foreach ($overrideable as $override) {
			if ($override->name == 'Test name') {
				$overrideMatches = true;
			}
		}
		$this->assertFalse($overrideMatches);

		// but can be overridden by firm
		$overrideable = PhraseByFirm::model()->getOverrideableNames($sectionId, $firmId);

		$firm = Firm::model()->findByPk($firmId);
		$overrideMatches = false;
		foreach ($overrideable as $override) {
			if ($override->name == 'Test name') {
				$overrideMatches = true;
			}
		}
		$this->assertTrue($overrideMatches);

		// override it by firm
		$phrase = new PhraseByFirm;
		$phrase->phrase = 'Test phrase two';
		$phrase->section_id = $sectionId;
		$phrase->firm_id = $firmId;
		$phrase->phrase_name_id = $phraseName->id;
		$phrase->save();

		// test that it now can't be overridden by firm
		$overrideable = PhraseByFirm::model()->getOverrideableNames($sectionId, $firmId);

		$overrideMatches = false;
		foreach ($overrideable as $override) {
			if ($override->name == 'Test name') {
				$overrideMatches = true;
			}
		}
		$this->assertFalse($overrideMatches);

		// remove the by specialty override
		$phraseBySpecialty = PhraseBySpecialty::model()->findByAttributes(array('phrase'=>'Test phrase two'));
		$phraseBySpecialty->delete();
		
		// test that it still can't be overridden by firm
		$overrideable = PhraseByFirm::model()->getOverrideableNames($sectionId, $firmId);

		$overrideMatches = false;
		foreach ($overrideable as $override) {
			if ($override->name == 'Test name') {
				$overrideMatches = true;
			}
		}
		$this->assertFalse($overrideMatches);
	}
}
