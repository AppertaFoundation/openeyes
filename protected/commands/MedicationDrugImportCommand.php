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
Import data from various different data sources into the Medication Drug model.

EOH;
	}

	public $defaultAction = 'import';

	public $type = 'vtm';
	public $external_source = 'dmd';
	public $import_size = 5;

	public $filter_list = array('Cotton garment','dressing','bandage','lancets', 'stockinette', 'needles', 'catheter',
			'device', 'gloves', 'Vaginal insert', 'pessary', 'wipes', 'needle', 'hosiery', 'syringe', 'adhesive', 'tops', 'baby grow',
			'shorts', 'leggings', 'vest', 'mittens', 'briefs', 'boxer shorts', 'nebuliser');

	public function actionImport($args)
	{
		$filename = $args[0];
		if (!$filename) {
			$this->usageError('Import filename required');
		}

		if (!file_exists($filename)) {
			$this->usageError("Cannot find import file " . $filename);
		}

		$connection = Yii::app()->db;
		$cmd = $connection->createCommand('ALTER TABLE medication_drug DISABLE KEYS;');
		$cmd->execute();

		$xr = new XMLReader();
		$xr->open($filename);
		$count = 0;
		$rows = array();
		$filter_regex = "/" . join('|', $this->filter_list) . "/i";

		switch ($this->type) {
			case 'vtm':
				// get to the start
				while ($xr->read() && $xr->name !== 'VTM');

				// iterate through
				while ($xr->name === 'VTM') {
					$node = new SimpleXMLElement($xr->readOuterXml());
					$rows[] = implode(",", array(
							$connection->quoteValue($node->NM),
							$connection->quoteValue($node->VTMID),
							$connection->quoteValue('DMD-VTM')));

					$xr->next('VTM');
					if ((++$count % $this->import_size) == 0) {
						$this->importMD($rows);
					}
				}
				break;
			case 'vmp':
				// get to the start
				while ($xr->read() && $xr->name !== 'VMP');

				// iterate through
				while ($xr->name === 'VMP') {
					$node = new SimpleXMLElement($xr->readOuterXml());
					if ($node->VTMID || preg_match($filter_regex, $node->NM)) {
						$xr->next('VMP');
						continue;
					}

					$rows[] = implode(',', array(
								$connection->quoteValue($node->NM),
								$connection->quoteValue($node->VPID),
								$connection->quoteValue('DMD-VMP')
							));
					$xr->next('VMP');
					if ((++$count % $this->import_size) == 0) {
						$this->importMD($rows);
					}
				}
				break;
			default:
				echo "Unrecognised format " . $this->type . "\n\n";
				echo $this->getHelp();
		}

		// be good
		$xr->close();

		// import remainder
		if (count($rows)) {
			$this->importMD($rows);
		}

		// turn the indexes back on
		$cmd = $connection->createCommand('ALTER TABLE medication_drug ENABLE KEYS;');
		$cmd->execute();

	}

	protected function importMD(&$rows)
	{
		$vals = implode('),(', $rows);
		$connection = Yii::app()->db;
		$cmd = $connection->createCommand('INSERT IGNORE INTO medication_drug (`name`,`external_code`,`external_source`) VALUES (' . $vals . ')');
		$cmd->execute();

		$rows = array();
	}


}