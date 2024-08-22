<?php

/**
 * OpenEyes.
 *
 * 
 * Copyright OpenEyes Foundation, 2017
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class ModuleAPI extends CApplicationComponent
{
    protected function getCurrentDataContext()
    {
        return new DataContext();
    }

    /**
     * @param $moduleName
     * @return BaseAPI|bool
     */
    public function get($moduleName, DataContext $data_context = null)
    {
        if ($data_context === null) {
            $data_context = $this->getCurrentDataContext();
        }

        if ($module = Yii::app()->getModule($moduleName)) {
            if ($et = EventType::model()->cache(1000)->find('class_name = ?', array($moduleName))) {
                // if the module has been inherited from, and has its own API, should return that instead
                if ($child = EventType::model()->cache(1000)->find('parent_event_type_id = ?', array($et->id))) {
                    if ($child_api = self::get($child->class_name, $data_context)) {
                        return $child_api;
                    }
                }
            }

            if (file_exists(Yii::getPathOfAlias("application.modules.{$moduleName}.components") . DIRECTORY_SEPARATOR . "{$moduleName}_API.php")) {
                $APIClass_prefix = '';
                $ns_components = explode('\\', get_class($module));
                if (count($ns_components) > 1) {
                    // we're namespaced so the class for the api will also be namespaced.
                    $APIClass_prefix = implode('\\',
                            array_slice($ns_components, 0, count($ns_components) - 1)) . '\components\\';
                }

                $APIClass = $APIClass_prefix . $moduleName . '_API';
                if (class_exists($APIClass)) {
                    return new $APIClass(Yii::app(), $data_context);
                }
            }
        }

        return false;
    }

    protected $_module_class_map;

    /**
     * Simple mapping function from module class name to it's id.
     *
     * @param $class_name
     *
     * @return mixed
     */
    public function moduleIDFromClass($class_name)
    {
        if (!$this->_module_class_map) {
            foreach (Yii::app()->getModules() as $id => $mc) {
                $this->_module_class_map[$mc['class']] = $id;
            }
        }
        return @$this->_module_class_map[$class_name];
    }

    /**
     * Convenience wrapper to retrieve the API relevant to the given event id
     *
     * @param $event_id
     * @return BaseAPI|bool
     */
    public function getForEventId($event_id)
    {
        if ($event = Event::model()->with('eventType')->findByPk($event_id)) {
            return $this->get($event->eventType->class_name);
        }
        return false;
    }
}
