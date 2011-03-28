<?php
class ContactTypeTest extends CDbTestCase
{
	public $fixtures = array(
		'contacttypes' => 'ContactType'
	);

	public function testNoContactTypes()
	{
		$this->assertEquals(8, count(ContactType::model()->findAll()));
	}
}