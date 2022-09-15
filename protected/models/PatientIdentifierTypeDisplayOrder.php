<?php
/**
 * OpenEyes.
 *
 * Copyright OpenEyes Foundation, 2020
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * This is the model class for table "patient_identifier_type_display_order".
 *
 * The followings are the available columns in table 'patient_identifier_type_display_order':
 * @property integer $id
 * @property integer $institution_id
 * @property string $display_order
 * @property string $patient_identifier_type_id
 * @property integer $site_id
 * @property string $necessity
 * @property string $status_necessity
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 * @property string $unique_row_string
 * @property bool $auto_increment
 * @property integer $auto_increment_start
 *
 * The followings are the available model relations:
 * @property PatientIdentifierType $patientIdentifierType
 * @property User $lastModifiedUser
 * @property User $createdUser
 */
class PatientIdentifierTypeDisplayOrder extends BaseActiveRecordVersioned
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'patient_identifier_type_display_order';
    }

    /**
     * @return array necessity options
     */
    public static function getNecessityOptions()
    {
        return [
            'hidden',
            'optional',
            'mandatory',
        ];
    }

    /**
     * @param string $necessity_value
     * @return string necessity label
     */
    public static function getNecessityLabel($necessity_value)
    {
        return ucfirst($necessity_value);
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['institution_id, display_order, patient_identifier_type_id, necessity, status_necessity', 'required'],
            ['necessity', 'validateNecessity'],
            ['status_necessity', 'validateNecessity'],
            ['search_protocol_prefix', 'length', 'max' => 100],
            ['institution_id, display_order, patient_identifier_type_id, necessity, status_necessity, site_id,
             search_protocol_prefix, searchable,unique_row_string, auto_increment, auto_increment_start', 'safe'],
        ];
    }

    public function validateNecessity($attribute, $params)
    {
        $options = $this->getNecessityOptions();
        if (!in_array($this->necessity, $options)) {
            $this->addError('necessity', 'Please choose a valid necessity value: ' . implode(", ", $options));
        }
    }

    public function relations()
    {
        return [
            'patientIdentifierType' => array(self::BELONGS_TO, 'PatientIdentifierType', 'patient_identifier_type_id'),
            'institution' => array(self::BELONGS_TO, 'Institution', 'institution_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
        ];
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'Id',
            'institution_id' => 'Institution',
            'display_order' => 'Display order',
            'patient_identifier_type_id' => 'Patient Identifier Type',
            'necessity' => 'Necessity',
            'status_necessity' => 'Status Necessity',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PatientIdentifierTypeDisplayOrder the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function getSearchProtocols(): array
    {
        $protocols = [];
        if ($this->search_protocol_prefix) {
            $protocol_list = explode("|", $this->search_protocol_prefix);
            foreach ($protocol_list as $item) {
                if (!in_array($item, $protocols)) {
                    $protocols[] = strtolower($item);
                }
            }
        }

        return $protocols;
    }

    public function beforeSave()
    {
        $unique_row_string_site_id = $this->site_id;
        if (!isset($this->site_id) || $this->site_id === '') {
            $unique_row_string_site_id = 0;
        }

        $this->unique_row_string = $this->patient_identifier_type_id . '-' . $this->institution_id . '-' . $unique_row_string_site_id;

        return parent::beforeSave();
    }
}
