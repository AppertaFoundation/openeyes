<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\CoverAndPrismCover_Entry;

class CoverAndPrismCover extends \BaseEventElementWidget
{
    public function renderEntriesForElement($entries)
    {
        foreach ($entries as $i => $entry) {
            $this->render($this->getViewForEntry(), $this->getViewDataForEntry($entry, (string) $i));
        }
    }

    public function renderEntryTemplate()
    {
        $this->render($this->getViewForEntry(), $this->getViewDataForEntry(new CoverAndPrismCover_Entry()));
    }

    /**
     * @param $entries
     * @return bool
     */
    public function shouldDisplayProViewForEntries($entries)
    {
        return count($entries) <= 1;
    }

    protected function ensureRequiredDataKeysSet(&$data)
    {
        if (!isset($data['entries'])) {
            $data['entries'] = [];
        }
    }

    /**
     * @param $attr
     * @return mixed|string
     */
    public function getReadingAttributeLabel($attr)
    {
        return CoverAndPrismCover_Entry::model()->getAttributeLabel($attr);

    }

    protected function getViewForEntry()
    {
        return $this->getViewNameForPrefix('CoverAndPrismCover_Entry');
    }

    protected function getViewDataForEntry(CoverAndPrismCover_Entry $entry, $index = '{{row_count}}')
    {
        return [
            'row_count' => $index,
            'field_prefix' => \CHtml::modelName($this->element) . "[entries][{$index}]",
            'entry' => $entry
        ];
    }
}