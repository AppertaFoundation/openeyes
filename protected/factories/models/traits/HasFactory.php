<?php
/**
 * (C) Copyright Apperta Foundation 2022
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

namespace OE\factories\models\traits;

use OE\factories\ModelFactory;

/**
 * This trait should be attached to any Model for which a factory is defined. It allows the following pattern
 *
 * ModelClass::factory()->create()
 *
 * to be called, irrespective of whether the model is namespaced or not.
 */
trait HasFactory
{
    protected static $reflection_class_for_factory;

    public static function factory()
    {
        return ModelFactory::factoryFor(get_called_class());
    }

    public static function factoryName()
    {
        if (preg_match('/OEModule/', static::class)) {
            return ModelFactory::buildModuleFactoryName(get_called_class());
        }

        $rc = static::reflectionClass();

        if (preg_match('/modules/', dirname($rc->getFileName()))) {
            return static::class . 'Factory';
        }

        return ModelFactory::$defaultModelNamespace . get_called_class() . 'Factory';
    }

    /**
     * This ensures that the factories/models path is imported
     * for instantiation in non-namespaced modules
     */
    public static function importNonNamespacedFactories()
    {
        if (preg_match('/OEModule/', static::class)) {
            return;
        }

        // assumed to not be a namespaced model class
        $rc = static::reflectionClass();
        $class_path = dirname($rc->getFileName());

        $path_segments = explode(DIRECTORY_SEPARATOR, $class_path);

        // get the module name from the file path (assumes models directory)
        $module_name = $path_segments[count($path_segments) - 2];
        if ($module_name !== 'protected') {
            \Yii::import("{$module_name}.factories.models.*");
        }
    }

    protected static function reflectionClass()
    {
        if (!static::$reflection_class_for_factory) {
            static::$reflection_class_for_factory = new \ReflectionClass(static::class);
        }

        return static::$reflection_class_for_factory;
    }
}
