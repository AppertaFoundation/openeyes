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

class BuildStringMapCommand extends CConsoleCommand
{
	public function getName()
	{
		return 'BuildStringMapCommand';
	}

	public function getHelp()
	{
		return Yii::t('strings','Greps through the codebase for all i18n string references and dumps them into a template as protected/messages/strings.sample.php').'.';
	}

	public function run($args)
	{
		$this->scan(dirname(__FILE__)."../../");
	}

	public function scan($dir) {
		$dh = opendir($dir);

		while ($file = readdir($dh)) {
			if (is_file($dir."/".$file)) {
				$this->process($dir."/".$file);
			} else if (is_dir($dir."/".$file)) {
				$this->scan($dir."/".$file);
			}
		}
	}

	public function process($file) {
		//foreach (@file($file
	}
}
