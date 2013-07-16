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

class ClientScript extends CClientScript
{
	public $cache_buster;

	/**
	 * Extending unifyScripts in order to hook the cache buster in at the right
	 * point in the render method
	 */
	protected function unifyScripts()
	{
		parent::unifyScripts();

		if ($this->cache_buster) {

			// JS
			foreach ($this->scriptFiles as $pos => $script_files) {
				foreach ($script_files as $key => $script_file) {
					// Add cache buster string to url
					$joiner = $this->getJoiner($script_file);
					$this->scriptFiles[$pos][$key] = $script_file . $joiner . $this->cache_buster;
				}
			}

			// CSS
			foreach ($this->cssFiles as $css_file => $media) {
				// Add cache buster string to url
				unset($this->cssFiles[$css_file]);
				$joiner = $this->getJoiner($css_file);
				$css_file = $css_file . '?' . $this->cache_buster;
				$this->cssFiles[$css_file] = $media;
			}

		}

	}

	protected function getJoiner($file)
	{
		if (preg_match('/\?/',$file)) {
			return '&';
		} else {
			return '?';
		}
	}

}
