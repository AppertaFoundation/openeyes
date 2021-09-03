<?php

/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\components\traits;


trait APICache
{
    protected static $data_cache = [];

    /**
     * Retrieve data from the static cache, or set it using the callback and return
     * Should be used to reduce calls to the data layer where possible
     *
     * @param $cache_key
     * @param $callback
     * @return mixed
     */
    protected function getCachedData($cache_key, $callback, $callback_params = [])
    {
        if (!array_key_exists($cache_key, static::$data_cache)) {
            static::$data_cache[$cache_key] = call_user_func_array($callback, $callback_params);
        }

        return static::$data_cache[$cache_key];
    }

    protected function resetCacheData($cache_key = null)
    {
        if ($cache_key === null) {
            static::$data_cache = [];
        } else {
            unset(static::$data_cache[$cache_key]);
        }
    }

    /**
     * Simple means of ensuring that the cache is not used - aimed primarily at testing environment
     * as the cache should generally remain valid during the request cycle it will exist for.
     */
    public static function clearDataCache()
    {
        static::$data_cache = [];
    }

    /**
     * Experimental to reduce queries for elements. May be better to make this simply
     * part of the core method
     *
     * @param $element
     * @param $patient
     * @param $use_context
     * @return mixed
     */
    protected function getCachedLatestElement($element, $patient, $use_context = false)
    {
        return $this->getCachedData(
            "getLatestElement-{$patient->id}-" . ($use_context ? "1" : "0") . "-" . $element,
            [$this, 'getLatestElement'],
            [$element, $patient, $use_context]
        );
    }
}