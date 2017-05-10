<?php
/**
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\Allergies as AllergiesElement;
use OEModule\OphCiExamination\models\AllergyEntry;

class Allergies extends \BaseEventElementWidget
{
    /**
     * @return FamilyHistoryElement
     */
    protected function getNewElement()
    {
        return new AllergiesElement();
    }

    /**
     * @param FamilyHistoryElement $element
     * @param $data
     * @throws \CException
     */
    protected function updateElementFromData($element, $data)
    {
        if  (!is_a($element, 'OEModule\OphCiExamination\models\Allergies')) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }

        if (array_key_exists('no_allergies', $data)) {
            // TODO: Think about the importance of this date information, and therefore whether it should
            // TODO: be preserved across change events for the family history
            if ($data['no_allergies'] == 1) {
                if (!$element->no_allergies_date) {
                    $element->no_allergies_date = date('Y-m-d H:i:s');
                }
            } else {
                $element->no_allergies_date = null;
            }
        }

        // pre-cache current entries so any entries that remain in place will use the same db row
        $entries_by_id = array();
        if (!$element->isNewRecord) {
            foreach ($element->entries as $entry) {
                $entries_by_id[$entry->id] = $entry;
            }
        }

        if (array_key_exists('allergy_id', $data)) {
            $entries = array();
            foreach ($data['allergy_id'] as $i => $allergy_id) {
                $entry = new AllergyEntry();
                $id = $data['id'][$i];
                if ($id && array_key_exists($id, $entries_by_id)) {
                    $entry = $entries_by_id[$id];
                }
                $entry->allergy_id = $allergy_id;
                $entry->other = $data['other'][$i];
                $entry->comments = $data['comments'][$i];
                $entries[] = $entry;
            }
            $element->entries = $entries;
        }
    }

    /**
     * Determine the view file to use
     */
    protected function getView()
    {
        if ($this->view_file) {
            // manually overridden/set
            return $this->view_file;
        }
        switch ($this->mode) {
            case static::$EVENT_VIEW_MODE:
                return 'Allergies_event_view';
                break;
            case static::$EVENT_EDIT_MODE:
                return 'Allergies_event_edit';
                break;
            default:
                return 'Allergies_patient_mode';
        }
    }

    /**
     * @return string
     */
    public function run()
    {
        return $this->render($this->getView(), array(
            'element' => $this->element,
            'form' => $this->form));
    }

}