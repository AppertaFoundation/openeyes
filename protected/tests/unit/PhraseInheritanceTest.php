<?php

class PhraseInheritanceTest extends CDbTestCase
{
	public $fixtures = array(
		'services' => 'Service',
		'specialties' => 'Specialty',
		'sections' => 'Section',
		'sectionTypes' => 'SectionType',
		'phrases' => 'Phrase',
		'phrasesBySpecialty' => 'PhraseBySpecialty',
		'phrasesByFirm' => 'PhraseByFirm',
		'firms' => 'Firm',
		'serviceSpecialtyAssignment' => 'ServiceSpecialtyAssignment',
	);

	public function testPhraseNames()
	{

	}

	public function testPhraseInheritance()
	{
		# create a Phrase with a new PhraseName for a particular section
		# make sure it's on the override list when creating a phrase by specialty for that section and a particular specialty  (PhraseBySpecialty::getOverrideableNames) [should be true]
		$sectionId = 1; $specialtyId = 1; $firmId = 1;

		$phraseName = new PhraseName;
		$phraseName->name = "Test name";
		$phraseName->save();
		$phraseName = PhraseName::model()->findByAttributes(array('name' => 'Test name'));

		# first make sure there is no match
		$overrideable = PhraseBySpecialty::model()->getOverrideableNames($sectionId, $specialtyId);

		$overrideMatches = false;
		foreach ($overrideable as $override) {
			if ($override->name == 'Test name') {
				$overrideMatches = true;
			}
		}
		$this->assertFalse($overrideMatches);

		# associate the name with a toplevel phrase
		$phrase = new Phrase;
		$phrase->phrase = 'Test phrase';
		$phrase->section_id = $sectionId;
		$phrase->phrase_name_id = $phraseName->id;
		$phrase->save();

		# test that it can now be overridden by specialty
		$overrideable = PhraseBySpecialty::model()->getOverrideableNames($sectionId, $specialtyId);

		$overrideMatches = false;
		foreach ($overrideable as $override) {
			if ($override->name == 'Test name') {
				$overrideMatches = true;
			}
		}
		$this->assertTrue($overrideMatches);

		# or by firm
		$overrideable = PhraseByFirm::model()->getOverrideableNames($sectionId, $firmId);

		$overrideMatches = false;
		foreach ($overrideable as $override) {
			if ($override->name == 'Test name') {
				$overrideMatches = true;
			}
		}
		$this->assertTrue($overrideMatches);

		# override the phrase by specialty
		$phrase = new PhraseBySpecialty;
		$phrase->phrase = 'Test phrase two';
		$phrase->section_id = $sectionId;
		$phrase->specialty_id = $specialtyId;
		$phrase->phrase_name_id = $phraseName->id;
		$phrase->save();

		# test that it can't now be overridden by specialty
		$overrideable = PhraseBySpecialty::model()->getOverrideableNames($sectionId, $specialtyId);

		$overrideMatches = false;
		foreach ($overrideable as $override) {
			if ($override->name == 'Test name') {
				$overrideMatches = true;
			}
		}
		$this->assertFalse($overrideMatches);

		# but can be overridden by firm
		$overrideable = PhraseByFirm::model()->getOverrideableNames($sectionId, $firmId);

		$firm = Firm::model()->findByPk($firmId);
		echo "Firm specialty is: " . $firm->serviceSpecialtyAssignment->specialty_id . "\n";
		$overrideMatches = false;
		foreach ($overrideable as $override) {
			echo "-" . $override->name . "-\n";
			if ($override->name == 'Test name') {
				$overrideMatches = true;
			}
		}
		$this->assertTrue($overrideMatches);

		# override it by firm
		$phrase = new PhraseByFirm;
		$phrase->phrase = 'Test phrase two';
		$phrase->section_id = $sectionId;
		$phrase->firm_id = $firmId;
		$phrase->phrase_name_id = $phraseName->id;
		$phrase->save();

		# test that it now can't be overridden by firm
		$overrideable = PhraseByFirm::model()->getOverrideableNames($sectionId, $firmId);

		$overrideMatches = false;
		foreach ($overrideable as $override) {
			if ($override->name == 'Test name') {
				$overrideMatches = true;
			}
		}
		$this->assertFalse($overrideMatches);

		# remove the by specialty override
		$phraseBySpecialty = PhraseBySpecialty::model()->findByAttributes(array('phrase'=>'Test phrase two'));
		$phraseBySpecialty->delete();
		
		# test that it still can't be overridden by firm
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
