<?php

/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class DrugTypeTest extends CDbTestCase
{
    /**
        * @var DrugType
        */
       protected $model;
    public $fixtures = array(
            'drugtypes' => 'DrugType',
       );

       /**
        * Sets up the fixture, for example, opens a network connection.
        * This method is called before a test is executed.
        */
       protected function setUp()
       {
           parent::setUp();
           $this->model = new DrugType();
       }

       /**
        * Tears down the fixture, for example, closes a network connection.
        * This method is called after a test is executed.
        */
       protected function tearDown()
       {
       }

       /**
        * @covers DrugType::model
        *
        * @todo   Implement testModel().
        */
       public function testModel()
       {
           $this->assertEquals('DrugType', get_class(DrugType::model()), 'Class name should match model.');
       }

       /**
        * @covers DrugForm::tableName
        *
        * @todo   Implement testTableName().
        */
       public function testTableName()
       {
           $this->assertEquals('drug_type', $this->model->tableName());
       }

       /**
        * @covers DrugForm::rules
        *
        * @todo   Implement testRules().
        */
       public function testRules()
       {
           $this->assertTrue($this->drugtypes('drugtype1')->validate());
           $this->assertEmpty($this->drugtypes('drugtype2')->errors);
       }


       /**
        * @covers DrugType::search
        *
        * @todo   Implement testSearch().
        */
       public function testSearch()
       {
           $this->model->setAttributes($this->drugtypes('drugtype1')->getAttributes());
           $results = $this->model->search();
           $data = $results->getData();

           $expectedKeys = array('drugtype1');
           $expectedResults = array();
           if (!empty($expectedKeys)) {
               foreach ($expectedKeys as $key) {
                   $expectedResults[] = $this->drugtypes($key);
               }
           }
           $this->assertEquals(1, $results->getItemCount());
           $this->assertEquals($expectedResults, $data);
       }
}
