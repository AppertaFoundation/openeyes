<?php
class ContacttypeTest extends CDbTestCase
{
	public $fixtures = array(
		'contacttypes' => 'Contacttype'
	);

	public function testNoContacttypes()
	{
		$this->assertEquals(8, count(Contacttype::model()->findAll()));
	}
}