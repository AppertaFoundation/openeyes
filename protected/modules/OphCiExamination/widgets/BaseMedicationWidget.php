<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\BaseMedicationElement;
use OEModule\OphCiExamination\models\MedicationManagementEntry;

abstract class BaseMedicationWidget extends \BaseEventElementWidget
{
    public static $moduleName = 'OphCiExamination';

    protected static $elementClass;

    public $notattip_edit_warning = 'OEModule.OphCiExamination.widgets.views.HistoryMedications_edit_nottip';
    public $is_latest_element = null;
    public $missing_prescription_items = null;

    public static $INLINE_EVENT_VIEW = 256;
    public static $PRESCRIPTION_PRINT_VIEW = 512;

    /**
     * @return BaseMedicationElement
     */

    protected function getNewElement()
    {
        $class = static::$elementClass;
        return new $class;
    }

    /**
		 * @param BaseMedicationElement $element
		 * @param $data
		 * @throws \CException
		 */
	protected function updateElementFromData($element, $data)
	{
		/*
		if(array_key_exists("do_not_save_entries", $data)) {
				$element->do_not_save_entries = (bool)$data['do_not_save_entries'];
		}
		*/

		if (!is_a($element, static::$elementClass)) {
			throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
		}

		/** @var BaseMedicationElement $element */

		// pre-cache current entries so any entries that remain in place will use the same db row
		$entries_by_id = array();
		foreach ($element->entries as $entry) {
			$entries_by_id[$entry->id] = $entry;
		}

		$entries = array();
		$to_prescription = array();

		if (array_key_exists('entries', $data)) {
			foreach ($data['entries'] as $i => $entry_data) {
				$id = array_key_exists('id', $entry_data) ? $entry_data['id'] : null;

				if ($id && array_key_exists($id, $entries_by_id)) {
					$entry = $entries_by_id[$id];
				} else {
					$class = $element::$entry_class;
					$entry = new $class;
				}

				/** @var \EventMedicationUse $entry */

				foreach (array_merge(
									 $entry->attributeNames(),
									 ['is_copied_from_previous_event', 'group'])
								 as $k) {
					if (array_key_exists($k, $entry_data) && in_array($k, $entry->attributeNames())) {

						if (in_array($k, ['prescribe', 'stop'])) {
							continue;
						}
						$entry->setAttribute($k, $entry_data[$k]);
					}
				}

				$entry->medication_name = isset($entry_data['medication_name']) ? $entry_data['medication_name'] : null;

				if (is_a($entry, MedicationManagementEntry::class)) {
					if (array_key_exists('prescribe', $entry_data)) {
						$entry->prescribe = $entry_data['prescribe'];
					} else {
						$entry->prescribe = 0;
					}
				}

				if (isset($entry_data['start_date']) && $entry_data['start_date'] !== '') {
					list($start_year, $start_month, $start_day) = array_pad(explode('-', $entry_data['start_date']), 3, null);
					$entry->start_date = \Helper::padFuzzyDate($start_year, $start_month, $start_day);
				}

				if (isset($entry_data['end_date']) && $entry_data['end_date'] !== '') {
					list($end_year, $end_month, $end_day) = array_pad(explode('-', $entry_data['end_date']), 3, null);
					$entry->end_date = \Helper::padFuzzyDate($end_year, $end_month, $end_day);
				} else {
					$entry->end_date = null;
				}

				if (in_array('prescribe', $entry->attributeNames()) && $entry->prescribe) {
					$entry->setScenario("to_be_prescribed");
				}

				$entry->originallyStopped = !array_key_exists("originallyStopped", $entry_data) ? false : (bool)$entry_data['originallyStopped'];

				if (property_exists($entry, "locked") && array_key_exists("locked", $entry_data)) {
					$entry->locked = $entry_data['locked'];
				}

				// add tapers

				if ($entry->taper_support) {
					$tapers = array();
					if (array_key_exists("taper", $entry_data)) {
						foreach ($entry_data['taper'] as $taper_data) {
							$taper = new \OphDrPrescription_ItemTaper();
							$taper->setAttributes($taper_data);
							$tapers[] = $taper;
						}
					}

					$entry->tapers = $tapers;
				}

				if ($entry->hidden) {
					$entry->setScenario("hidden");
				} elseif ($entry->getScenario() !== "to_be_prescribed") {
					$entry->setScenario("visible");
				}

				$entries[] = $entry;
			}

			$element->entries = $entries;
		} else {
			$element->entries = array();
		}

	}

    /**
     * @return bool Whether entries have been posted
     */

    protected function isPostedEntries()
    {
        $class_name_underscores = str_replace("\\", "_", static::$elementClass);
        return isset($_POST[$class_name_underscores]['entries']);
    }

    public function getViewData()
    {
        return array_merge(
            parent::getViewData(),
            array('prescribe_access' => $this->checkAccess('OprnCreatePrescription'))
        );
    }
}