<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class EpisodeSummaryItemTest extends CDbTestCase
{
	public $fixtures = array(
		'EventType',
		'EpisodeSummaryItem' => 'EpisodeSummaryItem',
		':episode_summary',
	);

	public function testEnabled_Default()
	{
		$this->assertEquals(
			array($this->EpisodeSummaryItem('bar'), $this->EpisodeSummaryItem('foo')),
			EpisodeSummaryItem::model()->enabled()->findAll()
		);
	}

	public function testEnabled_Subspecialty()
	{
		$this->assertEquals(
			array($this->EpisodeSummaryItem('baz')),
			EpisodeSummaryItem::model()->enabled(1)->findAll()
		);
	}

	public function testAvailable_Default()
	{
		$this->assertEquals(
			array($this->EpisodeSummaryItem('baz')),
			EpisodeSummaryItem::model()->available()->findAll()
		);
	}

	public function testAvailable_Subspecialty()
	{
		$this->assertEquals(
			array($this->EpisodeSummaryItem('bar'), $this->EpisodeSummaryItem('foo')),
			EpisodeSummaryItem::model()->available(1)->findAll()
		);
	}

	public function testAssign_Default()
	{
		EpisodeSummaryItem::model()->assign(array(3));
		$this->assertEquals(
			array($this->EpisodeSummaryItem('baz')),
			EpisodeSummaryItem::model()->enabled()->findAll()
		);
	}

	public function testAssign_Subspecialty()
	{
		EpisodeSummaryItem::model()->assign(array(1, 2), 1);
		$this->assertEquals(
			array($this->EpisodeSummaryItem('foo'), $this->EpisodeSummaryItem('bar')),
			EpisodeSummaryItem::model()->enabled(1)->findAll()
		);
	}

	public function testAssign_Default_Empty()
	{
		EpisodeSummaryItem::model()->assign(array());
		$this->assertEquals(
			array(),
			EpisodeSummaryItem::model()->enabled()->findAll()
		);
	}

	public function testAssign_Subspecialty_Empty()
	{
		EpisodeSummaryItem::model()->assign(array(), 1);
		$this->assertEquals(
			array(),
			EpisodeSummaryItem::model()->enabled(1)->findAll()
		);
	}

	public function testGetClassName()
	{
		$this->assertEquals(
			'OphCiExamination_Episode_Foo',
			$this->EpisodeSummaryItem('foo')->getClassName()
		);
	}
}
