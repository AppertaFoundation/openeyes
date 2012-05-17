<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class OECClientScript {
	/**
	 * Registers a CSS file and munges image asset paths as it goes
	 * @param string $url URL of the CSS file
	 * @param string $media media that the CSS file should be applied to. If empty, it means all media types.
	 * @return CClientScript the CClientScript object itself (to support method chaining, available since version 1.1.5).
	 */
	static public function registerCssFile($url,$media='')
	{
		$return = Yii::app()->clientScript->registerCssFile($url,$media);

		if (file_exists(getcwd().$url)) {
			$imgpath = Yii::app()->getController()->imgPath;

			file_put_contents(getcwd().$url, str_replace('/IMAGEASSETS/',$imgpath,file_get_contents(getcwd().$url)));
		}

		return $return;
	}
}
?>
