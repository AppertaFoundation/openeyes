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

    class MedicationManagementEntry extends \BaseElement
    {
        /**
         * Returns the static model of the specified AR class.
         *
         * @return static
         */
        public static function model($className = __CLASS__)
        {
            return parent::model($className);
        }

        /**
         * @return string the associated database table name
         */
        public function tableName()
        {
            return 'ophciexamination_medication_management_entry';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                array('ref_medication_id, start_date', 'required'),
                array('ref_medication_id, form_id, laterality, route_id, frequency_id, duration, stop_reason_id, stop, prescribe, continue', 'numerical', 'integerOnly'=>true),
                array('dose', 'numerical'),
                array('last_modified_user_id, created_user_id', 'length', 'max'=>10),
                array('dose_unit_term', 'length', 'max'=>45),
                array('start_date, end_date', 'length', 'max'=>8),
                array('last_modified_date, created_date, element_id', 'safe'),
                // The following rule is used by search().
                // @todo Please remove those attributes that should not be searched.
                array('id, element_id, ref_medication_id, form_id, laterality, dose, dose_unit_term, route_id, frequency_id, duration, start_date, end_date, last_modified_user_id, last_modified_date, created_user_id, created_date', 'safe', 'on'=>'search'),
            );
        }

        /**
         * @return array relational rules.
         */
        public function relations()
        {
            return array(
                'element' => array(self::BELONGS_TO, MedicationManagement::class, 'element_id'),
                'createdUser' => array(self::BELONGS_TO, \User::class, 'created_user_id'),
                'lastModifiedUser' => array(self::BELONGS_TO, \User::class, 'last_modified_user_id'),
                'form' => array(self::BELONGS_TO, \RefMedicationForm::class, 'form_id'),
                'frequency' => array(self::BELONGS_TO, \RefMedicationFrequency::class, 'frequency_id'),
                'refMedication' => array(self::BELONGS_TO, \RefMedication::class, 'ref_medication_id'),
                'route' => array(self::BELONGS_TO, \RefMedicationRoute::class, 'route_id'),
                'stopReason' => array(self::BELONGS_TO, HistoryMedicationsStopReason::class, 'stop_reason_id'),
                'refMedicationLaterality' => array(self::BELONGS_TO, \RefMedicationLaterality::class, 'laterality')
            );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
            return array(
                'id' => 'ID',
                'element_id' => 'Element',
                'ref_medication_id' => 'Ref Medication',
                'form_id' => 'Form',
                'laterality' => 'Laterality',
                'dose' => 'Dose',
                'dose_unit_term' => 'Dose Unit Term',
                'route_id' => 'Route',
                'frequency_id' => 'Frequency',
                'start_date' => 'Start Date',
                'end_date' => 'End Date',
                'last_modified_user_id' => 'Last Modified User',
                'last_modified_date' => 'Last Modified Date',
                'created_user_id' => 'Created User',
                'created_date' => 'Created Date',
                'stop' => 'Stop',
                'continue' => 'Continue',
                'prescribe' => 'Prescribe'
            );
        }

        public function getMedicationDisplay($short = false)
        {
            return $this->ref_medication_id ? ($short ? $this->refMedication->short_term : $this->refMedication->preferred_term) : '';
        }

        public function routeOptions()
        {
            return \RefMedicationLaterality::model()->findAll();
        }
    }