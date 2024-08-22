<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\Element_OphCiExamination_Investigation as InvestigationsElement;
use OEModule\OphCiExamination\models\OphCiExamination_Investigation_Entry;

class Investigations extends \BaseEventElementWidget
{
    public static $moduleName = 'OphCiExamination';

    protected function getNewElement()
    {
        return new InvestigationsElement();
    }

    protected function getView()
    {
        $viewPath = "application.modules.OphCiExamination.widgets.views";
        switch ($this->mode) {
            case static::$EVENT_EDIT_MODE:
                $viewFile =  'Element_OphCiExamination_Investigation';
                break;

            case static::$EVENT_VIEW_MODE:
            default:
                $viewFile =  'view_Element_OphCiExamination_Investigation';
                break;
        }

        return $viewPath.".".$viewFile;
    }


    /**
     * @param InvestigationsElement $element
     * @param $data
     * @throws \CException
     */
    protected function updateElementFromData($element, $data)
    {
        if (!is_a($element, 'OEModule\OphCiExamination\models\Element_OphCiExamination_Investigation')) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }

        // pre-cache current entries so any entries that remain in place will use the same db row
        $entries_by_id = array();
        foreach ($element->entries as $entry) {
            $entries_by_id[$entry->id] = $entry;
        }

        if (array_key_exists('entries', $data)) {
            $entries = array();
            foreach ($data['entries'] as $i => $entry) {
                $investigation = new InvestigationsElement();
                $investigation_entry = new OphCiExamination_Investigation_Entry();
                $id = $entry['id'];
                if ($id && array_key_exists($id, $entries_by_id)) {
                    $investigation_entry = $entries_by_id[$id];
                }
                $investigation_entry->investigation_code = $entry['investigation_code'];
                $investigation_entry->time = $entry['time'];
                $investigation_entry->date = $entry['date'];
                $investigation_entry->last_modified_user_id = $entry['last_modified_user_id'];
                $investigation_entry->comments = $entry['comments'];
                $entries[] = $investigation_entry;
            }
            $investigation->description = $data['description'];
            $element->entries = $entries;
            $element->description = $investigation->description;
        } else {
            $element->entries = array();
            $element->description = $data['description'] ?? null;
        }
    }
}
