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

/**
 * This is the module class for $this->moduleSuffix.
 *
 * The followings are the available columns in table:
 *
 * @property string $moduleShortSuffix
 */
class OphTrOperationbookingModule extends BaseEventTypeModule
{
    // this property is really only relevant to gii auto-generation, specifically
    // for updates to the module through gii
    public $moduleShortSuffix;
    public $default_parameter_settings = array(
            'OphTrOperationbooking_duplicate_proc_warn' => true,
            'OphTrOperationbooking_duplicate_proc_warn_all_eps' => true,
    );
    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application

        // import the module-level models and components
        $this->setImport(array(
            'OphTrOperationbooking.models.*',
            'OphTrOperationbooking.components.*',
            'OphTrOperationbooking.components.Exceptions',
            'OphTrOperationbooking.helpers.*',
            'OphTrOperationbooking.controllers.*',
            'OphTrOperationbooking.seeders.*',
        ));

        $this->moduleShortSuffix = 'operation';

        foreach ($this->default_parameter_settings as $k => $v) {
            if (!isset(Yii::app()->params[$k])) {
                Yii::app()->params[$k] = $v;
            }
        }
    }

    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     *
     * Returns true if Theatre Diary is disabled by system setting
     */

    public function isTheatreDiaryDisabled()
    {
        $element_enabled = Yii::app()->params['disable_theatre_diary'];
        if (is_null($element_enabled)) {
            $metadata = SettingMetadata::model()->find('`key`=?', array('disable_theatre_diary'));
            $setting = $metadata->getSetting($metadata->key, null, true);
            $element_enabled = $setting->value;
        }

        return isset($element_enabled) && $element_enabled == 'on';
    }

    /**
     * @return bool
     *
     * Returns true if the Golden Patient is disabled
     */

    public function isGoldenPatientDisabled()
    {
        return \SettingMetadata::model()->getSetting('op_booking_disable_golden_patient') == 'on';
    }

    /**
     * @return bool
     *
     * Returns true if the Anaesthetic Cover Required is disabled
     */

    public function isLACDisabled()
    {
        $lac_enabled = Yii::app()->params['op_booking_show_lac_required'];
        return isset($lac_enabled) && $lac_enabled == 'off';
    }
}

/**
 * Class RaceConditionException.
 *
 * Used for operation booking scheduling - a specific class of exception
 * to raise when two users attempt to schedule the same operation booking
 */
class RaceConditionException extends Exception
{
}
