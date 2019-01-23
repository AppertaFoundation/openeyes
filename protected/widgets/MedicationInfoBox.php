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
        public $medication_id;
        public $error = false;

        private $data;

        public function init()
        {
            $this->data = $this->getViewData($this->medication_id);
        }

        public function run()
        {
            echo '<i class="oe-i info pad small js-has-tooltip" data-tooltip-content="'.$this->getInfoBoxHTML().'"></i>';
        }

        public function getHTML()
        {
            return '<i class="oe-i info pad small js-has-tooltip" data-tooltip-content="'.$this->getInfoBoxHTML().'"></i>';
        }

        private function getInfoBoxHTML()
        {
            if($this->error) {
                return '<i>Error while retrieving data for medication.</i>';
            }
            else {
                $lines = [];
                $lines[]= '<b>'.$this->data['label'].'</b>';
                foreach ($this->data as $key => $value) {
                    if($key == "label") {
                        continue;
                    }
                    $lines[]="<b>$key:</b> ".htmlspecialchars($value, ENT_QUOTES, 'UTF-8');;
                }

                return implode("<br/>",  $lines);
            }
        }

        private function getViewData($medication_id)
        {
            if(!$medication = Medication::model()->findByPk($medication_id)) {
                $data = [];
                $this->error = true;
            }
            else {
                /** @var Medication $medication */
                $data = [
                    'label' => $medication->getLabel(),
                    'Code' => $medication->preferred_code,
                    'Sets' => implode(', ', array_map(function ($e){
                            return $e->name;
                        } , $medication->getMedicationSetsForCurrentSubspecialty())),
                    'Alternative terms' => $medication->alternativeTerms(),
                ];

                if($medication->vtm_term != "") {
                    $data['VTM Term'] = $medication->vtm_term;
                }

                if($medication->vmp_term != "") {
                    $data['VMP Term'] = $medication->vmp_term;
                }

                if($medication->amp_term != "") {
                    $data['AMP Term'] = $medication->amp_term;
                }
            }

            return $data;
        }
    }