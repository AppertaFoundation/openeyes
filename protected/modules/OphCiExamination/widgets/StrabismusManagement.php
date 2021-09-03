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

use OEModule\OphCiExamination\models\StrabismusManagement_Entry;
use OEModule\OphCiExamination\models\StrabismusManagement_Treatment;
use OEModule\OphCiExamination\models\StrabismusManagement_TreatmentReason;

class StrabismusManagement extends \BaseEventElementWidget
{

    public function renderEntriesForElement($entries)
    {
        foreach ($entries as $i => $entry) {
            $this->render($this->getViewForEntry(), $this->getViewDataForEntry($entry, (string) $i));
        }
    }

    public function renderEntryTemplate()
    {
        $this->render($this->getViewForEntry(), $this->getViewDataForEntry(new StrabismusManagement_Entry()));
    }

    public function getJsonTreatments()
    {
        return \CJSON::encode(
            array_map(
                function ($treatment) {
                    $optsByCol = $treatment->getOptionsByColumn();
                    return [
                        'id' => $treatment->id,
                        'value' => $treatment->name,
                        'reason_required' => $treatment->reason_required,
                        'columns' => [
                            [
                                'multiselect' => (bool) $treatment->column1_multiselect,
                                'options' => $optsByCol[0] ?? []
                            ],
                            [
                                'multiselect' => (bool) $treatment->column2_multiselect,
                                'options' => $optsByCol[1] ?? []
                            ]
                        ]
                    ];
                },
                StrabismusManagement_Treatment::model()->with('options')->findAll()
            )
        );
    }

    public function getJsonTreatmentReasons()
    {
        return \CJSON::encode(
            array_map(
                function ($reason) {
                    return [
                        'id' => $reason->id,
                        'value' => $reason->name
                    ];
                },
                StrabismusManagement_TreatmentReason::model()->findAll()
            )
        );
    }

    protected function getViewForEntry()
    {
        return $this->getViewNameForPrefix('StrabismusManagement_Entry');
    }

    protected function getViewDataForEntry(StrabismusManagement_Entry $entry, $index = '{{row_count}}')
    {
        return [
            'row_count' => $index,
            'field_prefix' => \CHtml::modelName($this->element) . "[entries][{$index}]",
            'entry' => $entry
        ];
    }
}
