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

	protected $required_cols = array('parent_disorder_id', 'subspecialty_code');

	public function actionImport($args)
	{
		$filename = $args[0];
		if (!$filename) {
			$this->usageError('Import filename required');
		}

		if (!file_exists($filename)) {
			$this->usageError("Cannot find import file " . $filename);
		}

		if (!$this->reset_parent) {
			$this->required_cols[] = 'disorder_id';
		}

		$connection = Yii::app()->db;
		$transaction = $connection->beginTransaction();
		try {
			SecondaryToCommonOphthalmicDisorder::model()->deleteAll();
			$file = file($filename);
			$columns = array();
			$count = 0;
			$warnings = array();
			foreach ($file as $index => $line) {
				if (!$index) {
					$columns = str_getcsv($line, ',', '"');
					foreach ($this->required_cols as $req) {
						if (!in_array($req, $columns)) {
							throw new Exception("Missing required column '{$req}' from file {$filename}");
						}
					}
				}
				else {
					if (!strlen(trim($line))) {
						// skip empty line
						continue;
					}
					$record = str_getcsv($line, ',', '"');
					$data = array();
					foreach ($columns as $i => $col) {
						$data[$col] = @$record[$i];
					}
					if (!$subspecialty = $this->getSubspecialty($data['subspecialty_code'])) {
						$warnings[] = "no subspecialty found for {$data['subspecialty_code']}";
					}
					else {
						if ($this->reset_parent) {
							$this->resetSubspecialty($subspecialty);
						}
						if ($cod = $this->getCOD($data['parent_disorder_id'], $subspecialty)) {
							if ($data['disorder_id'] && $st_disorder = $this->getDisorder($data['disorder_id'])) {
								$st = new SecondaryToCommonOphthalmicDisorder();
								$st->parent_id = $cod->id;
								$st->disorder_id = $st_disorder->id;
								$st->display_order = $this->getNextDisplayOrderForParent($cod);
								$st->save();
								$count++;
							}
							else {
								if ($data['disorder_id'] && !$this->reset_parent) {
									$warnings[] = "Cannot find disorder with id {$data['disorder_id']}";
								}
							}
						}
						else {
							$warnings[] = "{$data['parent_disorder_id']} not a common disorder for {$subspecialty->name}";
						}
					}
				}
			}
			echo "Committing changes ...\n";
			$transaction->commit();
			echo "{$count} records created\n";
			if ($warnings) {
				echo "There were " . count($warnings) . " warnings:\n";
				foreach ($warnings as $warn) {
					echo $warn . "\n";
				}
			}
		}
		catch (Exception $e) {
			$transaction->rollback();
			$this->usageError($e->getMessage());
		}
	}

	protected $disorders = array();

	/**
	 * Cache wrapper for searching for and retrieving disorder by id
	 * @param $id
	 * @return Disorder|null
	 */
	protected function getDisorder($id)
	{
		if (!array_key_exists($id, $this->disorders)) {
			$this->disorders[$id] = Disorder::model()->findByPk($id);
		}
		return $this->disorders[$id];
	}

	protected $subspecialty = array();

	/**
	 * Cache wrapper for searching and retrieving subspecialty by ref spec code
	 *
	 * @param $code
	 * @return Subspecialty|null
	 */
	protected function getSubspecialty($code)
	{
		if (!array_key_exists($code, $this->subspecialty)) {
			$this->subspecialty[$code] = Subspecialty::model()->findByAttributes(array('ref_spec' => $code));
		}
		return $this->subspecialty[$code];
	}

	protected $cod = array();
	/**
	 * If script is set up to define the COD it will create one if it can't be found.
	 *
	 * @param $disorder_id
	 * @param $subspecialty
	 */
	protected function getCOD($disorder_id, $subspecialty)
	{
		$key = "{$disorder_id}:{$subspecialty->id}";
		if (!array_key_exists($key, $this->cod)) {
			$criteria = new CDbCriteria;
			$criteria->addCondition('subspecialty_id = :si');
			$criteria->params[':si'] = $subspecialty->id;
			if ($disorder_id == 'null') {
				$criteria->addCondition('disorder_id is null');
			} else {
				$criteria->addCondition('disorder_id = :di');
				$criteria->params[':di'] = $disorder_id;
			}

			if (!$cod = CommonOphthalmicDisorder::model()->find($criteria) && $this->reset_parent) {
				$display_order = $this->getNextDisplayOrderForSubspecialty($subspecialty);
				$_disorder_id = Disorder::model()->findByPk($disorder_id) ? $disorder_id : null;

				$cod = new CommonOphthalmicDisorder();
				$cod->disorder_id = $_disorder_id;
				$cod->subspecialty_id = $subspecialty->id;
				$cod->save();
			}
			$this->cod[$key] = $cod;
		}
		return $this->cod[$key];
	}

	protected $reset_subspecialty_ids = array();

	/**
	 * Remmoves the parent list for the given subspecialty if it's not already been removed
	 * @param $subspecialty
	 */
	public function resetSubspecialty($subspecialty)
	{
		if (!in_array($subspecialty->id, $this->reset_subspecialty_ids)) {
			CommonOphthalmicDisorder::model()->deleteAllByAttributes(array('subspecialty_id' => $subspecialty->id));
			$this->reset_subspecialty_ids[] = $subspecialty->id;
		}
	}

	protected $display_order = array();

	/**
	 * Wrapper to track the display order for Common Disorders
	 *
	 * @param $subspecialty
	 * @return mixed
	 */
	public function getNextDisplayOrderForSubspecialty($subspecialty)
	{
		if (!array_key_exists($subspecialty->id, $this->display_order)) {
			$criteria = new CDbCriteria;
			$criteria->addCondition('subspecialty_id = :si');
			$criteria->params[':si'] = $subspecialty->id;
			$criteria->order = 'display_order desc';
			$criteria->limit = 1;
			if ($cod = CommonOphthalmicDisorder::model()->find($criteria)) {
				$this->display_order[$subspecialty->id] = $cod->display_order;
			}
			else {
				$this->display_order[$subspecialty->id] = 0;
			}
		}

		return ++$this->display_order[$subspecialty->id];
	}

	protected $second_display_order = array();

	/**
	 * Wrapper to handle ordering for secondary to disorders
	 *
	 * @param $cod
	 * @return mixed
	 */
	public function getNextDisplayOrderForParent($cod)
	{
		if (!array_key_exists($cod->id, $this->second_display_order)) {
			$criteria = new CDbCriteria;
			$criteria->addCondition('parent_id = :id');
			$criteria->params[':id'] = $cod->id;
			$criteria->order = 'display_order desc';
			$criteria->limit = 1;
			if ($st = SecondaryToCommonOphthalmicDisorder::model()->find($criteria)) {
				$this->second_display_order[$cod->id] = $st->display_order;
			}
			else {
				$this->second_display_order[$cod->id] = 0;
			}
		}

		return ++$this->second_display_order[$cod->id];
	}

}
