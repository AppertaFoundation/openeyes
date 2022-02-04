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

use OEModule\OphCiExamination\models\AdviceGiven as AdviceElement;
use OEModule\OphCiExamination\models\AdviceLeafletEntry;

class AdviceGiven extends \BaseEventElementWidget
{
    public static $moduleName = 'OphCiExamination';

    protected function getNewElement()
    {
        return new AdviceElement();
    }

    protected function getView()
    {

        switch ($this->mode) {
            case static::$EVENT_EDIT_MODE:
                $viewPath = "application.modules.OphCiExamination.widgets.views";
                $viewFile =  'AdviceGiven';
                break;
            case static::$EVENT_VIEW_MODE:
            default:
                $viewPath = "application.modules.OphCiExamination.views.default";
                $viewFile =  'view_AdviceGiven';
                break;
        }

        return $viewPath.".".$viewFile;
    }


    /**
     * @param AdviceElement $element
     * @param $data
     * @throws \CException
     */
    protected function updateElementFromData($element, $data)
    {
        if (!$element instanceof AdviceElement) {
            throw new \CException('invalid element class ' . get_class($element) . ' for ' . static::class);
        }

        // pre-cache current entries so any entries that remain in place will use the same db row
        $entries_by_id = array();
        foreach ($element->leaflets as $entry) {
            $entries_by_id[$entry->id] = $entry;
        }

        $entries = array();

        if (array_key_exists('leaflets', $data)) {
            foreach ($data['leaflets'] as $entry) {
                $leaflet_entry = new AdviceLeafletEntry();
                $id = $entry['id'];
                if ($id && array_key_exists($id, $entries_by_id)) {
                    $leaflet_entry = $entries_by_id[$id];
                }
                $leaflet_entry->leaflet_id = $entry['leaflet_id'];
                $leaflet_entry->last_modified_user_id = $entry['last_modified_user_id'];
                $entries[] = $leaflet_entry;
            }
        }
        $element->leaflets = $entries;
        $element->comments = $data['comments'];
    }
}
