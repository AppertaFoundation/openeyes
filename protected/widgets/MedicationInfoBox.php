<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class MedicationInfoBox extends \BaseCWidget
{
    const TYPE_LIGHT = 0;
    const TYPE_ADVANCED = 1;
    public $medication_id;
    public $error = false;
    public $type;
    private $data;

    public function init()
    {
        $this->type = $this->type ? $this->type : self::TYPE_LIGHT;
        $this->data = $this->getViewData($this->medication_id);
    }

    private function getViewData($medication_id)
    {
        $data = [];
        if (!$medication = Medication::model()->findByPk($medication_id)) {
            $this->error = true;
            return $data;
        }

        if ($this->type === self::TYPE_LIGHT) {
            if ($medication->isAMP()) {
                $data['Generic'] = $medication->vmp_term;
                $data['Moiety'] = $medication->vtm_term;
                return $data;
            }

            if ($medication->isVMP()) {
                $data['Moiety'] = $medication->vtm_term;
                return $data;
            }

            // VTMs : No tool-tip needed, these are self explanatory
            if ($medication->isVTM()) {
                return $data;
            }

            // for local no tooltip is needed
            return $data;
        }

        // if $this->type === self::TYPE_ADVANCED
        if (!$medication = Medication::model()->findByPk($medication_id)) {
            $data = [];
            $this->error = true;
        } else {

            $data = [
                'label' => $medication->getLabel(),
            ];

            if ($medication->isAMP()) {
                $data['Type'] = "Branded Product (AMP)";
                $data['Generic'] = $medication->vmp_term;
                $data['Moiety'] = $medication->vtm_term;
            } elseif ($medication->isVMP()) {
                $data['Type'] = "Generic Product (VMP)";
                $data['Moiety'] = $medication->vtm_term;
            } elseif ($medication->isVTM()) {
                $data['Type'] = "Virtual Therapeutic Moiety (VTM)";
            } elseif ($medication->source_type == Medication::SOURCE_TYPE_LOCAL) {
                $data['Type'] = "Local";
            }

            $data["Code"] = $medication->preferred_code ? $medication->preferred_code : "N/A";

            $data['Sets'] = implode(', ', array_map(function ($e) {
                return $e->name;
            }, $medication->getMedicationSetsForCurrentSubspecialty()));

            if ($data['Sets'] == "") {
                unset($data['Sets']);
            }
        }

        return $data;
    }

    public function run()
    {
        echo $this->data ?
            ('<i class="oe-i info pad small js-has-tooltip" data-tooltip-content="' . $this->getInfoBoxHTML() . '"></i>') : '';
    }

    private function getInfoBoxHTML()
    {
        if ($this->error) {
            return '<i>Error while retrieving data for medication.</i>';
        } else {
            $lines = [];
            if (isset($this->data['label'])) {
                $lines[] = '<b>' . $this->data['label'] . '</b>';
            }

            foreach ($this->data as $key => $value) {
                if ($key == "label") {
                    continue;
                }
                $lines[] = "<b>$key:</b> " . htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }

            return implode("<br/>", $lines);
        }
    }

    public function getHTML()
    {
        return $this->data ?
            ('<i class="oe-i info pad small js-has-tooltip" data-tooltip-content="' . $this->getInfoBoxHTML() . '"></i>') : '';
    }
}