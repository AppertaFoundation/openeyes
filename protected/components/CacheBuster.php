<?php
/**
* OpenEyes
*
* (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
* (C) OpenEyes Foundation, 2011-2013
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @package OpenEyes
* @link http://www.openeyes.org.uk
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
* @copyright Copyright (c) 2011-2013, OpenEyes Foundation
* @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
*/

/**
 * A basic CacheBuster component class that adds a cache busting string
 * to the end of a URL.
 */
class CacheBuster extends CApplicationComponent
{
	/**
	 * The time string to append to the URL.
	 * @var string
	 */
	public $time;

	/**
	 * Create a cache busted URL.
	 * @param  string $url  The URL to cache bust.
	 * @param  string $time The time string to append to the url.
	 * @return String       The cache busted URL.
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
	 * @param  string $url The URL to check.
	 * @return string      The joiner char (either '?' or '&')
	 */
	protected function getJoiner($url = '')
	{
		return preg_match('/\?/',$url) ? '&' : '?';
	}
}
