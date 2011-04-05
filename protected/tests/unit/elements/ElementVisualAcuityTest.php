<?php

class ElementVisualAcuityTest extends CDbTestCase
{
	public $fixtures = array(
		'events' => 'Event',
		'elements' => 'ElementVisualAcuity',
	);

	public function testGetVisualAcuityOptions()
	{
		$options = ElementVisualAcuity::model()->getVisualAcuityOptions(ElementVisualAcuity::SNELLEN_METRE);
		$this->assertTrue(is_array($options));
		$this->assertEquals(12, count($options));
	}

	public function testGetVisualAcuityText()
	{
		$element = ElementVisualAcuity::model()->findByPk(1);
		$this->assertEquals('3/60', $element->getVisualAcuityText(ElementVisualAcuity::SNELLEN_METRE, 'rva_ua'));
	}

	public function testGetAidOptions()
	{
		$options = ElementVisualAcuity::model()->getAidOptions();
		$this->assertTrue(is_array($options));
		$this->assertEquals(5, count($options));
	}

	public function testGetAidText()
	{
		$element = ElementVisualAcuity::model()->findByPk(1);
		$this->assertEquals('Glasses', $element->getAidText('right_aid'));
	}

	public function testDistanceOptions()
	{
		$options = ElementVisualAcuity::model()->getDistanceOptions(ElementVisualAcuity::SNELLEN_METRE);
		$this->assertTrue(is_array($options));
		$this->assertEquals(count($options), 2);
	}

	public function testDistanceText()
	{
		$element = ElementVisualAcuity::model()->findByPk(1);
		$this->assertEquals(6, $element->getDistanceText(ElementVisualAcuity::SNELLEN_METRE));
	}

	public function testToSnellenMetre()
	{
		$element = ElementVisualAcuity::model()->findByPk(1);
		$this->assertEquals('3/60', $element->toSnellenMetre(24));
	}

	public function testToSnellenFoot()
	{
		$element = ElementVisualAcuity::model()->findByPk(1);
		$this->assertEquals('20/15', $element->toSnellenFoot(95));
	}

	public function testToETDRS()
	{
		$element = ElementVisualAcuity::model()->findByPk(1);
		// Have to convert to string here else phpunit complains double 0.78 does not equal double 0.78!
		$this->assertEquals('0.78', (string)$element->toETDRS(50));
	}
}