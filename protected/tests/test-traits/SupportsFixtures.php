<?php
/**
 * (C) Apperta Foundation, 2022
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2022, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This trait has been implemented to provide backward compatibility to the fixture loading
 * pattern of legacy tests, whilst matching the new phpunit class signatures.
 */
trait SupportsFixtures
{
    /**
     * @var array a list of fixtures that should be loaded before each test method executes.
     * The array keys are fixture names, and the array values are either AR class names
     * or table names. If table names, they must begin with a colon character (e.g. 'Post'
     * means an AR class, while ':post' means a table name).
     * Defaults to false, meaning fixtures will not be used at all.
     */
    protected $fixtures;

    /**
     * PHP magic method.
     * This method is overridden so that named fixture data can be accessed like a normal property.
     * @param string $name the property name
     * @throws Exception if unknown property is used
     * @return mixed the property value
     */
    public function __get($name)
    {
        if (is_array($this->fixtures) && ($rows = $this->getFixtureData($name)) !== false) {
            return $rows;
        }

        throw new Exception("Unknown property '$name' for class '".get_class($this)."'.");
    }

    /**
     * PHP magic method.
     * This method is overridden so that named fixture ActiveRecord instances can be accessed in terms of a method call.
     * @param string $name method name
     * @param string $params method parameters
     * @throws Exception if unknown method is used
     * @return mixed the property value
     */
    public function __call($name, $params)
    {
        if (is_array($this->fixtures) && isset($params[0]) && ($record = $this->getFixtureRecord($name, $params[0])) !== false) {
            return $record;
        }

        throw new Exception("Unknown method '$name' for class '" . get_class($this) . "'.");
    }

    /**
     * @return CDbFixtureManager the database fixture manager
     */
    public function getFixtureManager()
    {
        return Yii::app()->getComponent('fixture');
    }

    /**
     * @param string $name the fixture name (the key value in {@link fixtures}).
     * @return array the named fixture data
     */
    public function getFixtureData($name)
    {
        return $this->getFixtureManager()->getRows($name);
    }

    /**
     * @param string $name the fixture name (the key value in {@link fixtures}).
     * @param string $alias the alias of the fixture data row
     * @return CActiveRecord the ActiveRecord instance corresponding to the specified alias in the named fixture.
     * False is returned if there is no such fixture or the record cannot be found.
     */
    public function getFixtureRecord($name, $alias)
    {
        return $this->getFixtureManager()->getRecord($name, $alias);
    }

    /**
     * Sets up the fixture before executing a test method.
     * If you override this method, make sure the parent implementation is invoked.
     * Otherwise, the database fixtures will not be managed properly.
     */
    protected function setUpSupportsFixtures()
    {
        if (is_array($this->fixtures)) {
            $this->getFixtureManager()->load($this->fixtures);
        }
    }
}