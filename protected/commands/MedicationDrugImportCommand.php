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

class MedicationDrugImportCommand extends CConsoleCommand
{
	public function getName()
	{
		return 'Medication Drug Import Command.';
	}

	public function getHelp()
	{
		return <<<EOH
Import data from various different data sources into the Medication Drug model. Implemented initially to provide support
for importing medication drugs from the DM+D dataset. Specifically, the XML vtm and vmp files.

Note, the import can be run multiple times without duplicating entries, because there is a unique constraint on the
external_code, and external_source attributes of the medication_drug table. This means that updates from the DM+D dataset
can easily be applied by running the same import.

Options:
type - The type of data being imported - currently supports vtm or vmp
external_source - the name of the source data, any string, defaults to dmd (up to different installations to be consistent)
import_size - the number of rows that will be batch inserted at one time, defaults to 20.
filter_list - array of words from drug names that should be filtered out from the import (only supported by vmp import).

Usage:

./yiic medicationdrugimport [options] import [datafile]

EOH;
	}

	public $defaultAction = 'import';

	public $type = 'vtm';
	public $external_source = 'dmd';
	public $import_size = 20;

	public function actionImport($args)
	{
		$filename = $args[0];
		if (!$filename) {
			$this->usageError('Import filename required');
		}

		if (!file_exists($filename)) {
			$this->usageError("Cannot find import file " . $filename);
		}

		$mdi = new MedicationDrugImport;
		$mdi->import($filename, $this->type, $this->external_source, $this->import_size);
	}
}
