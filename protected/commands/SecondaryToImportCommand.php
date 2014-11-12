<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

class SecondaryToImportCommand extends CConsoleCommand
{
	public function getName()
	{
		return 'Secondary To Common Ophthalmic Disorder Import Command.';
	}

	public function getHelp()
	{
		return <<<EOH
Import data from csv that defines disorders to appear as "secondary to" options for common ophthalmic disorders. Primarily this is useful
for importing secondary to options where data has already been set up for the common opthalmic disoders. The secondary to feature was a later
addition to the drop down list short cut in diagnosis selection.

if reset_parent is set to true, then all current common disorders for any subspecialty in the import file will be removed.

EOH;
	}

	public $reset_parent = false;
	public $defaultAction = 'import';

	public function actionImport($args)
	{
		$filename = $args[0];
		if (!$filename) {
			$this->usageError('Import filename required');
		}

		if (!file_exists($filename)) {
			$this->usageError("Cannot find import file " . $filename);
		}

		$sti = new SecondaryToImport;
		$sti->import($filename, $this->reset_parent);
	}
}
