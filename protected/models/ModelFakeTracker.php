<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This static class is used during testing to support the resolution of CModel classes to fakes.
 *
 * It should not be invoked directly, instead the trait CanBeFaked should be applied to the Model you wish
 * to fake, and the methods made available through that trait should be used.
 *
 * The intent here is to make the cleaning up of any fakes very simple during between tests.
 */
class ModelFakeTracker
{
    public static array $faked_models = [];

    public static function setFakeForModel(string $model_class, $fake = null): void
    {
        if (!$fake && array_key_exists($model_class, self::$faked_models)) {
            unset(static::$faked_models[$model_class]);
            return;
        }

        static::$faked_models[$model_class] = $fake;
    }

    public static function getFakeForModel(string $model_class)
    {
        return static::$faked_models[$model_class] ?? null;
    }

    public static function reset(): void
    {
        static::$faked_models = [];
    }
}
