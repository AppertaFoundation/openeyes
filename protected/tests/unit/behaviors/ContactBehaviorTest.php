<?php
class _WrapperContactBehavior extends BaseActiveRecord
{
	public $contact;

	public function tableName()
	{
		return 'contact';
	}

	public function behaviors()
	{
		return array(
			'ContactBehavior' => array(
				'class' => 'application.behaviors.ContactBehavior',
			),
		);
	}

	public function getTelephone()
	{
		return '01234 567890';
	}

	public function getFax()
	{
		return '09876 543210';
	}

	public function getPrefix()
	{
		return "Excuse me I'm a tad unwell";
	}
}

class _WrapperContactBehavior2 extends BaseActiveRecord
{
	public $contact;
	public $date_of_death = '2013-04-01';

	public function tableName()
	{
		return 'contact';
	}

	public function behaviors()
	{
		return array(
			'ContactBehavior' => array(
				'class' => 'application.behaviors.ContactBehavior',
			),
		);
	}

	public function getTelephone()
	{
		return '01234 567890';
	}

	public function getFax()
	{
		return '09876 543210';
	}

	public function getPrefix()
	{
		return "Excuse me I'm a tad unwell";
	}

	public function getLetterArray()
	{
		return array("Four calling birds","Three french hens","Two turtle doves","Pheasant in a tree that grows pears");
	}

	public function getCorrespondenceName()
	{
		return 'General Sir Anthony Cecil Hogmanay Melchett';
	}

	public function getSalutationName()
	{
		return 'Kleptocrat';
	}
}

class _WrapperContactBehavior3 extends BaseActiveRecord
{
	public $contact;

	public function tableName()
	{
		return 'contact';
	}

	public function behaviors()
	{
		return array(
			'ContactBehavior' => array(
				'class' => 'application.behaviors.ContactBehavior',
			),
		);
	}

	public function getTelephone()
	{
		return '01234 567890';
	}

	public function getFax()
	{
		return '09876 543210';
	}

	public function getPrefix()
	{
		return "Excuse me I'm a tad unwell";
	}

	public function getLetterArray()
	{
		return array("Four calling birds","Three french hens","Two turtle doves","Pheasant in a tree that grows pears");
	}

	public function getCorrespondenceName()
	{
		return array('PHP','Is','Fantastic');
	}
}

class ContactBehaviorTest extends PHPUnit_Framework_TestCase
{
	private $model;
	public $fixtures = array(
		'contact' => 'Contact',
		'address' => 'Address',
		'country' => 'Country'
	);

	public function setUp()
	{
		$this->model = new _WrapperContactBehavior;

		$this->address = new Address;
		$this->address->attributes = array(
			'address1' => 'Line 1',
			'address2' => 'Line 2',
			'city' => 'City',
			'postcode' => 'Postcode',
			'county' => 'County',
			'country_id' => 1,
		);

		$label = new ContactLabel;
		$label->name = 'Test Label';

		$contact = ComponentStubGenerator::generate(
			'Contact',
			array(
				'address' => $this->address,
				'label' => $label,
				'fullName' => 'Henry Krinkle',
				'reversedFullName' => 'Krinkle Henry',
			)
		);

		$this->model->contact = $contact;

		$this->model2 = new _WrapperContactBehavior2;
		$this->model2->contact = $contact;

		$contact3 = ComponentStubGenerator::generate(
			'Contact',
			array(
				'address' => $this->address,
				'label' => $label,
				'fullName' => 'Henry Krinkle',
				'nick_name' => 'Jimbob',
			)
		);

		$this->model3 = new _WrapperContactBehavior3;
		$this->model3->contact = $contact3;
	}

	public function tearDown()
	{
	}

	public function testGetLetterAddressNoParams()
	{
		$this->assertEquals(array(
				'Line 1',
				'Line 2',
				'City',
				'County',
				'Postcode',
			),
			$this->model->getLetterAddress()
		);
	}

	public function testGetLetterAddressWithLabel()
	{
		$this->assertEquals(array(
				'Test Label',
				'Line 1',
				'Line 2',
				'City',
				'County',
				'Postcode',
			),
			$this->model->getLetterAddress(array('include_label'=>true))
		);
	}

	public function testGetLetterAddressWithCountry()
	{
		$this->markTestIncomplete('Currently this is failing for me. Anyone readying please help me and debug me');

		$this->assertEquals(array(
				'Line 1',
				'Line 2',
				'City',
				'County',
				'Postcode',
				'United Kingdom',
			),
			$this->model->getLetterAddress(array('include_country'=>true))
		);
	}

	public function testGetLetterAddressWithName()
	{
		$this->assertEquals(array(
				'Henry Krinkle',
				'Line 1',
				'Line 2',
				'City',
				'County',
				'Postcode',
			),
			$this->model->getLetterAddress(array('include_name'=>true))
		);
	}

	public function testGetLetterAddressWithTelephone()
	{
		$this->assertEquals(array(
				'Line 1',
				'Line 2',
				'City',
				'County',
				'Postcode',
				'Tel: 01234 567890',
			),
			$this->model->getLetterAddress(array('include_telephone'=>true))
		);
	}

	public function testGetLetterAddressWithFax()
	{
		$this->assertEquals(array(
				'Line 1',
				'Line 2',
				'City',
				'County',
				'Postcode',
				'Fax: 09876 543210',
			),
			$this->model->getLetterAddress(array('include_fax'=>true))
		);
	}

	public function testGetLetterAddressWithDelimiter()
	{
		$this->assertEquals(
			'Line 1 *COUGH* Line 2 *COUGH* City *COUGH* County *COUGH* Postcode',
			$this->model->getLetterAddress(array('delimiter'=>' *COUGH* '))
		);
	}

	public function testGetLetterAddressWithPrefix()
	{
		$this->assertEquals(
			"Excuse me I'm a tad unwell: Line 1 *COUGH* Line 2 *COUGH* City *COUGH* County *COUGH* Postcode",
			$this->model->getLetterAddress(array(
				'delimiter'=>' *COUGH* ',
				'include_prefix' => "Excuse me I'm a tad unwell",
			))
		);
	}

	public function testGetLetterAddressWithOwnerGetLetterArray()
	{
		$this->assertEquals(array(
				'Four calling birds',
				'Three french hens',
				'Two turtle doves',
				'Pheasant in a tree that grows pears',
			),
			$this->model2->getLetterAddress()
		);
	}

	public function testGetLetterAddressWithCorrespondenceNameAsString()
	{
		$this->assertEquals(array(
				'General Sir Anthony Cecil Hogmanay Melchett',
				'Four calling birds',
				'Three french hens',
				'Two turtle doves',
				'Pheasant in a tree that grows pears',
			),
			$this->model2->getLetterAddress(array('include_name' => true))
		);
	}

	public function testGetLetterAddressWithCorrespondenceNameAsArray()
	{
		$this->assertEquals(array(
				'PHP',
				'Is',
				'Fantastic',
				'Four calling birds',
				'Three french hens',
				'Two turtle doves',
				'Pheasant in a tree that grows pears',
			),
			$this->model3->getLetterAddress(array('include_name' => true))
		);
	}

	public function testGetLetterAddressWithAllTheTrimmings()
	{
		$this->markTestIncomplete('Currently this is failing for me. Anyone readying please help me and debug me');
		$this->assertEquals(array(
				'Henry Krinkle',
				'Test Label',
				'Line 1',
				'Line 2',
				'City',
				'County',
				'Postcode',
				'United Kingdom',
				'Tel: 01234 567890',
				'Fax: 09876 543210',
			),
			$this->model->getLetterAddress(array(
				'include_country' => true,
				'include_label' => true,
				'include_name' => true,
				'include_telephone' => true,
				'include_fax' => true,
			))
		);
	}

	public function testGetLetterIntroductionNoParams()
	{
		$this->assertEquals('Dear Sir/Madam,',$this->model->getLetterIntroduction());
	}

	public function testGetLetterIntroductionNicknameNotPresent()
	{
		$this->assertEquals('Dear Sir/Madam,',$this->model->getLetterIntroduction(array('nickname' => true)));
	}

	public function testGetLetterIntroductionWithSalutationName()
	{
		$this->assertEquals('Dear Kleptocrat,',$this->model2->getLetterIntroduction());
	}

	public function testGetLetterIntroductionNickname()
	{
		$this->assertEquals('Dear Jimbob,',$this->model3->getLetterIntroduction(array('nickname' => true)));
	}

	public function testGetFullName()
	{
		$this->assertEquals('Henry Krinkle',$this->model->getFullName());
	}

	public function testGetReversedFullName()
	{
		$this->assertEquals('Krinkle Henry',$this->model->getReversedFullName());
	}

	public function testGetSalutationName()
	{
		$this->assertEquals('Dear Kleptocrat,',$this->model2->getLetterIntroduction());
	}

	public function testIsDeceasedNull()
	{
		$this->assertEquals(null,$this->model->isDeceased());
	}

	public function testIsDeceasedPatientDied()
	{
		$this->assertEquals(true,$this->model2->isDeceased());
	}

	public function testGetPrefix()
	{
		$this->assertEquals("Excuse me I'm a tad unwell",$this->model->getPrefix());
	}
}
