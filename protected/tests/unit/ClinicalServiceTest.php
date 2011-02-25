<?php
class ClincalServiceTest extends CDbTestCase
{
	public $fixtures = array(
		'eventTypes' => ':event_type',
		'elementTypes' => ':element_type',
		'possibleElementTypes' => ':possible_element_type',
		'siteElementTypes' => ':site_element_type'
	);

	public function testgetSiteElementTypeObjects()
	{
		$this->assertTrue(true);
	}
}