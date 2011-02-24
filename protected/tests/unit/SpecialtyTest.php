<?php
class SpecialtyTest extends CDbTestCase
{
	public $fixtures = array(
		'specialties' => 'Specialty'
	);

	/**
	 * @dataProvider dataProvider_Search
	 */
	public function testGetDefaultElementTypeIds()
	{
	}
}