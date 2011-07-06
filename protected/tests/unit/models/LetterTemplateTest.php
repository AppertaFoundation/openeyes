<?php

class LetterTemplateTest extends CDbTestCase
{
	public $fixtures = array(
		'specialties' => 'Specialty',
		'contacttypses' => 'ContactType',
		'letterTemplates' => 'LetterTemplate'
	);

	public function testGetSpecialtyOptions()
	{
		$specialties = LetterTemplate::model()->getSpecialtyOptions();
		$this->assertTrue(is_array($specialties));
		$this->assertEquals(16, count($specialties));
	}

	public function testGetContactTypeOptions()
	{
		$contactTypes = LetterTemplate::model()->getContactTypeOptions();
		$this->assertTrue(is_array($contactTypes));
		$this->assertEquals(8, count($contactTypes));
	}

        public function testGetSpecialtyText()
        {
                $letterTemplate = LetterTemplate::model()->findByPk(1);

                $this->assertEquals($letterTemplate->getSpecialtyText(), 'Accident & Emergency');
        }

        public function testGetCCText()
        {
		$letterTemplate = LetterTemplate::model()->findByPk(1);

                $this->assertEquals($letterTemplate->getCcText(), 'Optometrist');
        }

        public function testGetToText()
        {
                $letterTemplate = LetterTemplate::model()->findByPk(1);

                $this->assertEquals($letterTemplate->getToText(), 'Ophthalmologist');
        }
}
