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
 * This static class is used during testing to support the resolution of classes to fakes.
 *
 * It should not be invoked directly, instead the trait CanBeFaked should be applied to the class you wish
 * to fake, and the methods made available through that trait should be used.
 *
 * note for CModel classes, you should use the ModelCanBeFaked trait instead.
 *
 * The intent here is to make the cleaning up of any fakes very simple during between tests.
 */
class FakedClassesTracker
{
    public static array $faked_classes = [];

    public static function setFakeForClass(string $class, $fake = null): void
    {
        if (!$fake && array_key_exists($class, self::$faked_classes)) {
            unset(static::$faked_classes[$class]);
            return;
        }

        static::$faked_classes[$class] = $fake;
    }

    public static function getFakeForClass(string $class)
    {
        return static::$faked_classes[$class] ?? null;
    }

    public static function reset(): void
    {
        static::$faked_classes = [];
    }
}
