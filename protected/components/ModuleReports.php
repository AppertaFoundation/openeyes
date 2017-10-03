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

/**
 * ModuleAdmin functions.
 */
class ModuleReports
{
    public static function getAll()
    {
        $reports = array();

        $module_classes = array();

        foreach (EventType::model()->findAll(array('order' => 'name')) as $event_type) {
            foreach (Yii::app()->params['reports'] as $item => $uri) {
                if (preg_match('/^\/'.$event_type->class_name.'\//', $uri)) {
                    $reports[$event_type->name][$item] = $uri;
                }
            }
            $module_classes[] = $event_type->class_name;
        }

        foreach (Yii::app()->modules as $module => $stuff) {
            if (!in_array($module, $module_classes)) {
                foreach (Yii::app()->params['reports'] as $item => $uri) {
                    if (preg_match('/^\/'.$module.'\//', $uri)) {
                        $reports[$module][$item] = $uri;
                    }
                }
            }
        }

        return $reports;
    }
}
