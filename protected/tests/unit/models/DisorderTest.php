<?php

class DisorderTest extends CDbTestCase
{
	public $fixtures = array(
		'disorders' => 'Disorder'
	);

	public function testGetDisorderOptions()
	{
		$disorders = Disorder::getDisorderOptions('mellitus');
		$this->assertTrue(is_array($disorders));
		$this->assertEquals(2, count($disorders));
	}
}
