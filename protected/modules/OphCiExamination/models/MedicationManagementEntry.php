<?php

    /**
     * OpenEyes.
     *
     * (C) OpenEyes Foundation, 2018
     * This file is part of OpenEyes.
     * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
     * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
     * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
     *
     * @link http://www.openeyes.org.uk
     *
     * @author OpenEyes <info@openeyes.org.uk>
     * @copyright Copyright (c) 2018, OpenEyes Foundation
     * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
     */

    namespace OEModule\OphCiExamination\models;

    class MedicationManagementEntry extends \EventMedicationUse
    {
        public static function getUsageType()
        {
            return "OphCiExamination";
        }

        public static function getUsageSubtype()
        {
            return "Management";
        }

        /**
         * Returns the static model of the specified AR class.
         */

        public static function model($className = __CLASS__)
        {
            return parent::model($className);
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array_merge(parent::relations(), array(
                // TODO define element relation
            ));
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array_merge(parent::attributeLabels(), array(
                'continue' => 'Continue',
                'prescribe' => 'Prescribe'
            ));
        }

        private function explodeDate($date_str)
        {
            return substr($date_str, 0, 4)."-".substr($date_str, 4, 2)."-".substr($date_str, 6, 2);
        }
    }