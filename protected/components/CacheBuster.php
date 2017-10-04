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
 * A basic CacheBuster component class that adds a cache busting string
 * to the end of a URL.
 */
class CacheBuster extends CApplicationComponent
{
    /**
     * The time string to append to the URL.
     *
     * @var string
     */
    public $time;

    /**
     * Create a cache busted URL.
     *
     * @param string $url  The URL to cache bust.
     * @param string $time The time string to append to the url.
     *
     * @return string The cache busted URL.
     */
    public function createUrl($url = '', $time = null)
    {
        $time = $time ?: $this->time;

        if ($time) {
            $joiner = $this->getJoiner($url);
            $url .= $joiner.$time;
        }

        return $url;
    }

    /**
     * Determine the joiner required to append the cache busting string. This checks
     * if the URL contains query string params and returns the appropriate joiner.
     *
     * @param string $url The URL to check.
     *
     * @return string The joiner char (either '?' or '&')
     */
    protected function getJoiner($url = '')
    {
        return preg_match('/\?/', $url) ? '&' : '?';
    }
}
