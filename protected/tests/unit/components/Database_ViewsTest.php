<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use PHPUnit\Framework\TestCase;

/*
* @group sample-data
*/
class DatabaseViewsTest extends PHPUnit_Framework_TestCase
{
    private ?CDbConnection $db = null;
    /**
     * Returns the currently active database connection.
     * By default, the 'db' application component will be returned and activated.
     * You can call {@link setDbConnection} to switch to a different database connection.
     * Methods such as {@link insert}, {@link createTable} will use this database connection
     * to perform DB queries.
     * @throws CException if "db" application component is not configured
     * @return CDbConnection the currently active database connection
     */
    public function getDbConnection()
    {
        if ($this->db === null) {
            $this->db = Yii::app()->getComponent('db');
            if (!$this->db instanceof CDbConnection) {
                throw new CException(Yii::t('yii', 'The "db" application component must be configured to be a CDbConnection object.'));
            }
        }
        return $this->db;
    }

    public function __construct()
    {
        $this->getDbConnection();
        parent::__construct();
    }


    public function getViews()
    {
         return $this->db->createCommand("SELECT TABLE_NAME 
                                FROM information_schema.tables 
                                WHERE TABLE_TYPE LIKE 'VIEW'
                                    AND TABLE_SCHEMA = DATABASE()")->queryAll();
    }

    /**
     * Checks that all all views return results without error.
     * If there are any missing or incorrectly named columns in the view definition, then an exception is thrown and the test will fail
     * @throws CException if view definition cannot be processed
     */
    public function testViews()
    {
        $views = $this->db->createCommand("SELECT TABLE_NAME 
                                FROM information_schema.tables 
                                WHERE TABLE_TYPE LIKE 'VIEW'
                                    AND TABLE_SCHEMA = DATABASE()")->queryAll();

        foreach ($views as $view) {
            $current = $view['TABLE_NAME'];
            $result = $this->db->createCommand("SELECT * FROM `" . $view['TABLE_NAME'] . "` LIMIT 1")->execute();
            if ($result >= 0) {
                $result = $current;
            }
            $this->assertEquals($current, $result);
        }

    /**
     * Detailed tests of specific views can be added here if desired
     */
    }
}
