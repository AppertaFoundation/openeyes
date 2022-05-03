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
class BaseModuleWidget extends BaseCWidget
{
    /**
     * @var array JS scripts to be loaded with the widget
     */
    public $js = [];

    public function init()
    {
        if (is_object($this->element) && $this->field) {
            $this->value = $this->element->{$this->field};
        }

        // load all js files required by $this->js
        $assetManager = Yii::app()->getAssetManager();
        foreach ($this->js as $js_name) {
            if (file_exists($this->getDir() . '/js/' . $js_name . '.js')) {
                $assetManager->registerScriptFile('js/' . $js_name . '.js', 'application.modules.' . \Yii::app()->controller->module->id . '.widgets', $this->scriptPriority);
            }
        }

        $this->htmlOptions['autocomplete'] = SettingMetadata::model()->getSetting('html_autocomplete');
    }

    public function getDir()
    {
        $reflector = new ReflectionClass(static::class);
        $file_name = $reflector->getFileName();
        return dirname($file_name);
    }
}
