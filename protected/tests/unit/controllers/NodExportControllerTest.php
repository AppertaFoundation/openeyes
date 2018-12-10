<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2014
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
class NodExportControllerTest extends CDbTestCase
{
    protected $date;
    protected $mock;
    protected $controller;

    public function setUp()
    {
        parent::setUp();

        $this->date = $date = date('YmdHi');
        $this->mock = $this->getMockBuilder('NodExportController')->disableOriginalConstructor()->getMock();

        $this->mock->method('createAllTempTables');

        $reflectionClass = new ReflectionClass('NodExportController');

        $institutionCode = $reflectionClass->getProperty('institutionCode');
        $institutionCode->setAccessible(true);
        $institutionCode->setValue($this->mock, Yii::app()->params['institution_code']);

        $exportPath = $reflectionClass->getProperty('exportPath');
        $exportPath->setAccessible(true);
        $exportPath->setValue($this->mock, (realpath(dirname(__FILE__).'/../../../').'/runtime/nod-export/test/'.$institutionCode->getValue($this->mock).'/'.$this->date));

        $zipName = $reflectionClass->getProperty('zipName');
        $zipName->setAccessible(true);
        $zipName->setValue($this->mock, ($institutionCode->getValue($this->mock).'_'.$this->date.'_NOD_Export.zip'));

        $this->controller = $reflectionClass;
        $this->exportPath = $exportPath->getValue($this->mock);
        $this->zipName = $zipName->getValue($this->mock);

        if (!file_exists($exportPath->getValue($this->mock))) {
            mkdir($exportPath->getValue($this->mock), 0777, true);
        }

        $createAllTempTablesmethod = $this->controller->getMethod('createAllTempTables');
        $createAllTempTablesmethod->setAccessible(true);
        $createAllTempTablesmethod->invoke($this->mock);
    }

        /**
         * Checking if the string actually is a float value like "24.6"
         * note: floatval("24.0") returns 24.
         * 
         * @param string $string
         *
         * @return bool if the string is a float value
         */
        private function isStringFloat($string)
        {
            if (is_numeric($string) && strpos($string, '.') !== false) {
                return true;
            }

            return false;
        }

        /**
         * Fetch the header of the CSV file (first line).
         * 
         * @param string $file path and name
         *
         * @return type
         */
        protected function getCSVHeader($file)
        {
            $file = fopen($file, 'r');

            $data = fgetcsv($file);
            fclose($file);

            return $data;
        }

        /**
         * Validate date structure.
         * 
         * @param string $date
         *
         * @return bool
         */
        public function validateDate($format, $date)
        {
            $d = DateTime::createFromFormat($format, $date);

            return $d && $d->format($format) == $date;
        }



    public function tearDown()
    {
        parent::tearDown();

        $clearAllTempTablesmethod = $this->controller->getMethod('clearAllTempTables');
        $clearAllTempTablesmethod->setAccessible(true);
        $clearAllTempTablesmethod->invoke($this->mock);
    }
}
