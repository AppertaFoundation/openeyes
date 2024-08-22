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
    private $icon = 'info';
    private $append_label = [];

    public function init()
    {
        $this->type = $this->type ? $this->type : self::TYPE_LIGHT;

        // Becuase the medication metadata rarely changes, it should be safe to cache for a decent amount of time
        $cache_key = "MedicationInfoBox_data_id:" . $this->medication_id . "_type:" . $this->type . "_firm_id:" . Yii::app()->session['selected_firm_id'] . "_site_id:" . Yii::app()->session['selected_site_id'];
        $this->data = Yii::app()->cache->get($cache_key);
        if ($this->data === false) {
            $this->data = $this->getViewData($this->medication_id);
            Yii::app()->cache->set($cache_key, $this->data, 1000);
        }
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getAppendLabel()
    {
        return implode('<br />', $this->append_label);
    }



    private function getViewData($medication_id)
    {
        $data = [];
        $this->append_label = [];
        if (!$medication = Medication::model()->findByPk($medication_id)) {
            $this->error = true;
            return $data;
        }

        // cache the firm for a few minutes, as this loop runs many times, and the firm data rarely changes
        $firm = Firm::model()->cache(300, null, 2)->findByPk(Yii::app()->session->get('selected_firm_id'))->with('ServiceSubspecialtyAssignment');
        $subspecialty_id = $firm->serviceSubspecialtyAssignment->subspecialty_id;
        $site_id = Yii::app()->session->get('selected_site_id');

        $alt_terms = $medication->alternativeTerms();
        if ($alt_terms !== '') {
            $data['Aliases'] = $alt_terms;
        }

        if ($this->type === self::TYPE_LIGHT) {
            if ($medication->isAMP()) {
                $data['Generic'] = isset($medication->vmp_term) ? $medication->vmp_term : "N/A";
                //$data['Moiety'] = isset($medication->vtm_term) ? $medication->vtm_term : "N/A";
            }

            // VMPs : No tool-tip needed as the moiety is usually part of he name
            // Left commented out for now, as at time of wrinting (27/03/2020) DA had not decided if they definitely do not want to show or not
            // if ($medication->isVMP()) {
            //     $data['Moiety'] = isset($medication->vtm_term) ? $medication->vtm_term : "N/A";
            // }

            // VTMs : No tool-tip needed, these are self explanatory
            // for local no tooltip is needed
        }

        if ($this->type === self::TYPE_ADVANCED) {
            if (!$medication = Medication::model()->findByPk($medication_id)) {
                $data = [];
                $this->error = true;
            } else {
                $data = [
                    'label' => $medication->getLabel(true)
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
        }

        foreach ($medication->medicationSets as $sets) {
            if ($sets->hasUsageCode('Formulary', $site_id, $subspecialty_id)) {
                $this->icon = 'formulary';

                //if  ( count($data) > 0 ) {$this->append_label[] ="<br/>";}
                $this->append_label[] = "<i class='oe-i formulary pad small'></i><em>In hospital formulary.</em>";

                break;
            }
        }

        return $data;
    }

    public function run()
    {
        $cache_key = "MedicationInfoBox_HTML_id:" . $this->medication_id . "_type:" . $this->type . "_firm_id:" . Yii::app()->session['selected_firm_id'] . "_site_id:" . Yii::app()->session['selected_site_id'];
        if ($this->beginCache($cache_key, array('duration' => 1000))) {
            echo $this->getHTML();
            $this->endCache();
        }
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
                if ($key == "label" || $key == "append-label") {
                    continue;
                }
                $lines[] = "<b>$key:</b> " . htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }

            $append_l[] = $this->getAppendLabel();

            // add a blank line between the data and the append label (only when there is data)
            if (!empty($lines)) {
                $lines[] = "";
            }
            $lines = array_merge($lines, $append_l);

            if ($lines) {
                return implode("<br/>", $lines);
            }

            return null;
        }
    }

    public function getHTML()
    {
        $content = $this->getInfoBoxHTML();
        return ($content) ?
            ('<i class="oe-i ' .  $this->icon . ' pad small js-has-tooltip" data-tooltip-content="' . $content . '"></i>') : '';
    }
}
