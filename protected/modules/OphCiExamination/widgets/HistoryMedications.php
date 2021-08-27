<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\widgets;

use CClientScript;
use CHttpException;
use EventMedicationUse;
use OEModule\OphCiExamination\models\HistoryMedications as HistoryMedicationsElement;
use OEModule\OphCiExamination\models\MedicationManagement as MedicationManagementElement;
use Yii;

/**
 * Class HistoryMedications
 * @package OEModule\OphCiExamination\widgets
 * @property HistoryMedicationsElement $element
 */
class HistoryMedications extends BaseMedicationWidget
{
    public static $INLINE_EVENT_VIEW = 256;
    public static $PRESCRIPTION_PRINT_VIEW = 512;

    protected static $elementClass = HistoryMedicationsElement::class;
    protected $print_view = 'HistoryMedications_event_print';

    public static $moduleName = 'OphCiExamination';
    public $notattip_edit_warning = 'OEModule.OphCiExamination.widgets.views.HistoryMedications_edit_nottip';
    public $is_latest_element = null;
    public $missing_prescription_items = null;
    public $pro_theme;


    /**
     * @return bool
     */
    protected function showViewTipWarning()
    {
        return $this->mode === static::$EVENT_VIEW_MODE;
    }

    /**
     * @throws CHttpException
     */
    public function init()
    {
        parent::init();

        // add OpenEyes.UI.RestrictedData js
        $assetManager = Yii::app()->getAssetManager();
        $baseAssetsPath = Yii::getPathOfAlias('application.assets.js');
        $assetManager->publish($baseAssetsPath, true);

        Yii::app()->clientScript->registerScriptFile($assetManager->getPublishedUrl($baseAssetsPath, true) . '/OpenEyes.UI.RestrictData.js', CClientScript::POS_END);
    }

    /**
     * @return bool
     * @inheritdoc
     */
    protected function isAtTip()
    {
        $this->is_latest_element = parent::isAtTip();
        // if it's a new record we trust that the missing prescription items will be added
        // to the element, otherwise we care if there are untracked prescription items
        // in terms of this being considered a tip record.
        if ($this->is_latest_element && $this->element->isNewRecord) {
            return true;
        }
        $this->missing_prescription_items = (bool) $this->element->getEntriesForUntrackedPrescriptionItems($this->patient);
        foreach ($this->element->entries as $entry) {
            if ($entry->prescriptionNotCurrent()) {
                return false;
            }
        }
        return !$this->missing_prescription_items;
    }

    /**
     * @return array
     */

    private function getEntriesFromPreviousManagement()
    {
        $entries = [];
        $element = $this->element->getModuleApi()->getLatestElement(MedicationManagementElement::class, $this->patient);
        if (!is_null($element)) {
            /** @var MedicationManagementElement $element */
            foreach ($element->entries as $entry) {
                if (!$entry->prescribe) {
                    /** @var EventMedicationUse $new_entry */
                    $new_entry = clone $entry;
                    if (!$this->inSummaryOrViewMode()) {
                        $new_entry->id = null;
                    }
                    $new_entry->setIsNewRecord(true);
                    $entries[] = $new_entry;
                }
            }
        }

        return $entries;
    }

    public function getEntriesFromPreviousHistory()
    {
        $entries = [];
        $element = $this->element->getModuleApi()->getLatestElement(HistoryMedicationsElement::class, $this->patient);
        if (!is_null($element)) {
            /** @var HistoryMedicationsElement $element*/
            $entries = $element->entries;
        }

        return $entries;
    }

    /**
     * @return bool whether any entries were set
     */

    private function setEntriesWithPreviousManagement()
    {
        $management_entries = $this->getEntriesFromPreviousManagement();
        $history_entries = $this->getEntriesFromPreviousHistory();
        foreach ($management_entries as $management_entry) {
            $duplicate = false;
            foreach ($history_entries as $entry) {
                if ($entry->bound_key === $management_entry->bound_key) {
                    $duplicate = true;
                    break;
                }
            }

            if (!$duplicate) {
                $history_entries[] = $management_entry;
            }
        }
        $this->element->entries = array_merge($history_entries, $this->element->getEntriesForUntrackedPrescriptionItems($this->patient));
    }

    /**
     * @inheritdoc
     */
    protected function setElementFromDefaults()
    {
        if (!$this->isPostedEntries()) {
            $this->setEntriesWithPreviousManagement();
            if ($this->inEditMode()) {
                foreach ($this->element->entries as $entry) {
                    $entry->id = null;
                }
            }

            // because the entries cloned into the new element may contain stale data for related
            // prescription data (or that prescription item might have been deleted)
            // we need to update appropriately.
            $entries = array();
            foreach ($this->element->entries as $entry) {
                if ($entry->prescription_item_id) {
                    if ($entry->prescription_event_deleted || !$entry->prescriptionItem) {
                        continue;
                    }
                    $entry->loadFromPrescriptionItem($entry->prescriptionItem);
                }
                $entries[] = $entry;
            }
            $untracked = $this->element->getEntriesForUntrackedPrescriptionItems($this->patient);
            if ($untracked) {
                // tracking prescription items.
                $this->element->entries = array_merge(
                    $entries,
                    $untracked
                );
            }
        }
    }

    /**
     * @return array
     */

    public function getMergedManagementEntries()
    {
        $this->setElementFromDefaults();

        $this->element->assortEntries();

        return [
            'current' => array_merge($this->element->current_entries, $this->element->prescribed_entries),
            'stopped' => $this->element->closed_entries,
        ];
    }

    /**
     * Merges any missing (i.e. created since the element) prescription items into lists of
     * current and stopped medications for patient level rendering.
     *
     * Expected to only be used with the at tip element, but would work with older elements as well
     * should the need arise.
     *
     * @return array
     */
    public function getMergedEntries()
    {
        $this->setElementFromDefaults();


        $this->element->assortEntries();
        $result['current'] = $this->element->current_entries;
        $result['stopped'] = $this->element->closed_entries;
        $result['prescribed'] = $this->element->prescribed_entries;

        // now remove any that are no longer relevant because the prescription item
        // has been deleted
        $filter = function ($entry) {
            return !($entry->prescription_item_deleted || $entry->prescription_event_deleted);
        };

        $result['current'] = array_filter($result['current'], $filter);
        $result['stopped'] = array_filter($result['stopped'], $filter);
        $result['prescribed'] = array_filter($result['prescribed'], $filter);

        $result['current'] = array_merge($result['current'], $result['prescribed']);

        return $result;
    }

    /**
     * @return string
     */
    public function popupList()
    {
        return $this->render($this->getView(), $this->getViewData());
    }

    /**
     * @return string
     * @inheritdoc
     */
    protected function getView()
    {
        // custom mode for rendering in the patient popup because the data is more complex
        // for this history element than others which just provide a list.
        $short_name = substr(strrchr(get_class($this), '\\'), 1);
        if ($this->mode === static::$PATIENT_POPUP_MODE) {
            return $short_name . '_patient_popup';
        }
        if ($this->mode === static::$INLINE_EVENT_VIEW) {
            return $short_name . '_inline_event_view';
        }
        if ($this->mode === static::$PRESCRIPTION_PRINT_VIEW) {
            return $short_name . '_prescription_print_view';
        }
        return parent::getView();
    }

    /**
     * @return array
     */
    public function getViewData()
    {
        if (in_array($this->mode, array(static::$PATIENT_SUMMARY_MODE, static::$PATIENT_LANDING_PAGE_MODE, static::$PATIENT_SUMMARY_MODE_OUTPUT))) {
            return array_merge(parent::getViewData(), $this->getMergedManagementEntries());
        }
        return parent::getViewData();
    }

    /**
     * @throws CHttpException
     */
    protected function initialiseElement()
    {
        if (!$this->element) {
            if (!$this->patient) {
                throw new CHttpException('Patient required to initialise ' . static::class . ' with no element.');
            }

            if ($this->mode != self::$EVENT_EDIT_MODE) {
                // must be in a view mode so just load the most recent
                $this->element = $this->getNewElement()->getMostRecentForPatient($this->patient);
            } else {
                $this->element = $this->getNewElement();
            }
        }

        // when new we want to always set to default so we can track changes
        // but if this element already exists then we don't want to override
        // it with the tip data
        if ($this->element && $this->element->getIsNewRecord() && $this->mode === self::$EVENT_EDIT_MODE) {
            $this->setElementFromDefaults();
        }

        if ($this->data) {
            // we set the element to the provided data
            $this->updateElementFromData($this->element, $this->data);
        }
        $this->element->widget = $this;
    }

    /**
     * @param $mode
     * @return bool
     * @inheritdoc
     */
    protected function validateMode($mode)
    {
        return in_array(
            $mode,
            array(static::$PRESCRIPTION_PRINT_VIEW, static::$INLINE_EVENT_VIEW),
            true
        ) || parent::validateMode($mode);
    }

    /**
     * @return HistoryMedicationsElement
     */
    protected function getNewElement()
    {
        return new HistoryMedicationsElement();
    }

    public function sortEntriesByDate($entries, bool $current = true) : array {
        return parent::sortEntriesByDate($entries, $current);
    }
}
