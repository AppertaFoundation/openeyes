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
class ModuleAdmin
{
    public static function getAll()
    {
        $module_admin = array();

        $module_classes = array();

        foreach (EventType::model()->findAll(array('order' => 'name')) as $event_type) {

            foreach (Yii::app()->params['admin_menu'] as $item => $uri) {

                if (is_array($uri) && !isset($uri['requires_setting'])) {
                    foreach ($uri as $key => $value) {
                        if ($event_type->class_name == 'OphCiExamination') {
                            $module_admin[$event_type->name][$item] = $value;
                        }
                    }
                }
                elseif(is_array($uri) && isset($uri['requires_setting'])) {

                    $setting_key = $uri['requires_setting']['setting_key'];
                    $required_value = $uri['requires_setting']['required_value'];
                    $item_enabled = Yii::app()->params[$setting_key];
                    
                    if (isset($item_enabled) && $item_enabled == $required_value) {
                        if(preg_match('/^\/' . $event_type->class_name . '\//', $uri['uri']))
                        {
                            $module_admin[$event_type->name][$item] = $uri['uri'];
                        }
                    }
                }
                else{
                    if(preg_match('/^\/' . $event_type->class_name . '\//', $uri))
                    {
                        $module_admin[$event_type->name][$item] = $uri;
                    }
                }

                $module_classes[] = $event_type->class_name;
            }
        }

        foreach (Yii::app()->modules as $module => $stuff) {
            if (!in_array($module, $module_classes)) {
                foreach (Yii::app()->params['admin_menu'] as $item => $uri) {
                    if (!is_array($uri)) {
                        if (preg_match('/^\/'.$module.'\//', $uri)) {
                            $module_admin[$module][$item] = $uri;
                        }
                    }
                }
            }
        }

        return $module_admin;
    }
}
