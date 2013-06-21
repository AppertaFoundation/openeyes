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

class DateLetterSentTest extends CDbTestCase {

      /**
       * @var DateLetterSent
       */
      protected $model;

      /**
       * Sets up the fixture, for example, opens a network connection.
       * This method is called before a test is executed.
       */
      protected function setUp() {
            
            parent::setUp();
            $this->model = new DateLetterSent;
      }

      /**
       * Tears down the fixture, for example, closes a network connection.
       * This method is called after a test is executed.
       */
      protected function tearDown() {
            
      }

      /**
       * @covers DateLetterSent::model
       * @todo   Implement testModel().
       */
      public function testModel() {
            
            $this->assertEquals('DateLetterSent', get_class(Address::model()), 'Class name should match model.');
      }

      /**
       * @covers DateLetterSent::tableName
       * @todo   Implement testTableName().
       */
      public function testTableName() {
            
            $this->assertEquals('date_letter_sent', $this->model->tableName());
            
      }

      /**
       * @covers DateLetterSent::rules
       * @todo   Implement testRules().
       */
      public function testRules() {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
                      'This test has not been implemented yet.'
            );
      }

      /**
       * @covers DateLetterSent::relations
       * @todo   Implement testRelations().
       */
      public function testRelations() {
            
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
                      'This test has not been implemented yet.'
            );
      }

      /**
       * @covers DateLetterSent::attributeLabels
       * @todo   Implement testAttributeLabels().
       */
      public function testAttributeLabels() {
            
            $expected = array(
			'id' => 'ID',
			'element_operation_id' => 'Operation',
			'date_invitation_letter_sent' => 'Date invitation letter sent',
			'date_1st_reminder_letter_sent' => 'Date 1st reminder letter sent',
			'date_2nd_reminder_letter_sent' => 'Date 2nd reminder letter sent',
			'date_gp_letter_sent' => 'Date GP letter sent',
			'date_scheduling_letter_sent' => 'Date scheduling letter sent',
		);
            
      }

      /**
       * @covers DateLetterSent::search
       * @todo   Implement testSearch().
       */
      public function testSearch() {
            // Remove the following lines when you implement this test.
            $this->markTestIncomplete(
                      'This test has not been implemented yet.'
            );
      }

}
