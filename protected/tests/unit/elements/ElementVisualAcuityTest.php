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