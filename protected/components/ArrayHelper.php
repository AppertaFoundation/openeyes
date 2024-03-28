<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2024
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2024, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class ArrayHelper
{
    public static function getOffsetOfArrayKey(array $array, $key)
    {
        return array_search($key, array_keys($array), true);
    }

    public static function insertAtKey($array, $item, $attribute, $key)
    {
        if (!isset($array[$key])) {
            $array[$key] = $item;
            return $array;
        }

        $offset = self::getOffsetOfArrayKey($array, $key);

        if (strcmp($array[$key]->$attribute, $item->$attribute) < 1 ) {
            $offset++;
            $first_part = array_slice($array, 0, $offset, true);
            $second_part = [$key => $item] + array_slice($array, $offset, null, true);
        } else {
            $first_part = array_slice($array, 0, $offset, true) + [$key => $item];
            $second_part = array_slice($array, $offset, null, true);
        }

        $result = [];

        foreach ($first_part as $key => $value) {
            $result[$key] = $value;
        }

        foreach ($second_part as $key => $value) {
            while (array_key_exists($key, $result)) {
                $key++;
            }
            $result[$key] = $value;
        }

        return $result;
    }
}
