<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2020
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

use OE\factories\models\traits\HasFactory;

/**
 * This is the model class for table "patient_identifier".
 *
 * The followings are the available columns in table 'patient_identifier':
 * @property string $patient_id
 * @property string $patient_identifier_type_id
 * @property string $value
 * @property string $last_modified_user_id
 * @property string $last_modified_date
 * @property string $created_user_id
 * @property string $created_date
 * @property string $unique_row_string
 * @property PatientIdentifierType $patientIdentifierType
 *
 * The followings are the available model relations:
 * @property User $lastModifiedUser
 * @property User $createdUser
 */
class PatientIdentifier extends BaseActiveRecordVersioned
{
    use HasFactory;

    public $status_is_mandatory = false;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'patient_identifier';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $rules = array(
            ['patient_id, patient_identifier_type_id, value', 'required'],
            ['patient_id, patient_identifier_type_id, value, source_info, deleted, last_modified_date, created_date, unique_row_string,id ', 'safe'],
            ['value', 'valueValidator'],
            ['patient_identifier_status_id', 'statusValidator'],
        );

        return $rules;
    }

    public function statusValidator($attribute, $params)
    {
        if ($this->status_is_mandatory) {
            $validator = CValidator::createValidator('required', $this, $attribute, $params);
            $validator->validate($this);
        }
    }

    public function valueValidator($attribute, $params)
    {
        if (!$this->patientIdentifierType->validateTerm($this->value)) {
            $this->addError($attribute, "Invalid value format. Acceptable: {$this->patientIdentifierType->validate_regex}");
        }

        $patient_id = isset($this->patient) ? $this->patient->id : null;
        if (!empty(Patient::findDuplicatesByIdentifier($this->patient_identifier_type_id, $this->value, $patient_id))) {
            $this->addError($attribute, $this->patientIdentifierType->short_title . ' must be unique');
        }
    }

    public function nhsNumValidator($attribute, $params)
    {
        $type = PatientIdentifierType::model()->findByPk($this->patient_identifier_type_id);
        // Validation only triggers for Australia
        if (Yii::app()->params['default_country'] === 'Australia' && $type->short_name === 'nhs_num') {
            // Throw validation warning message if user has entered non-numeric character
            if (!ctype_digit($this->value) && strlen($this->value) > 0) {
                $this->addError($attribute, 'Please enter only numbers.');
            }

            $medicareNo = preg_replace("/[^\d]/", "", $this->value);

            // Check for 11 digits
            $length = strlen($medicareNo);

            if ($length == 11) {
                // Unique check
                $count = Yii::app()->db->createCommand()
                    ->select('COUNT(p.id)')
                    ->from('patient p')
                    ->join('patient_identifier pi', 'p.id = pi.patient_id')
                    ->join('patient_identifier_type ptt', 'pi.patient_identifier_type_id = ptt.id')
                    ->where('LOWER(pi.value) = LOWER(:value) and p.id != COALESCE(:patient_id, "")',
                        array(':value' => $this->value, ':patient_id' => $this->patient_id))
                    ->queryScalar();

                if (count($count) !== 0) {
                    $this->addError($attribute, 'Duplicate Medicare Number entered.');
                }

                // Test leading digit and checksum
                if (preg_match("/^([2-6]\d{7})(\d)/", $medicareNo, $matches)) {
                    $base = $matches[1];
                    $checkDigit = $matches[2];
                    $sum = 0;
                    $weights = array(1, 3, 7, 9, 1, 3, 7, 9);
                    foreach ($weights as $position => $weight) {
                        $sum += $base[$position] * $weight;
                    }
                    return ($sum % 10) == intval($checkDigit);
                } else {
                    $this->addError($attribute, 'Not a valid Medicare Number');
                }
            } else {
                $this->addError($attribute, 'Not a valid Medicare Number');
            }
        }
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'patient' => array(self::BELONGS_TO, 'Patient', 'patient_id'),
            'patientIdentifierType' => array(self::BELONGS_TO, 'PatientIdentifierType', 'patient_identifier_type_id'),
            'lastModifiedUser' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
            'createdUser' => array(self::BELONGS_TO, 'User', 'created_user_id'),
            'patientIdentifierStatus' => array(self::BELONGS_TO, 'PatientIdentifierStatus', 'patient_identifier_status_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'patient_id' => 'Patient',
            'patient_identifier_type_id' => 'Patient Identifier Type',
            'patient_identifier_status_id' => 'Patient Identifier Status',
            'value' => 'Value',
            'source_info' => 'Source Info',
            'deleted' => 'Deleted',
            'last_modified_user_id' => 'Last Modified User',
            'last_modified_date' => 'Last Modified Date',
            'created_user_id' => 'Created User',
            'created_date' => 'Created Date',
        );
    }

    public function hasValue()
    {
        return isset($this->value) && trim($this->value) !== '';
    }

    public function getDisplayValue()
    {
        $spacing_rule = $this->patientIdentifierType->spacing_rule;
        $formatted_value = '';

        if ($spacing_rule) {
            $index = 0;
            $spacing_rule_array = str_split($spacing_rule);
            foreach ($spacing_rule_array as $char) {
                if ($index < strlen($this->value)) {
                    if ($char !== ' ') {
                        $formatted_value .= $this->value[$index];
                        $index++;
                    } else {
                        $formatted_value .= ' ';
                    }
                } else {
                    break;
                }
            }

            if ($index < strlen($this->value)) {
                $formatted_value .= ' ' . substr($this->value, $index);
            }
        } else {
            $formatted_value = $this->value;
        }

        return $this->patientIdentifierType->getDisplayIdentifierPrefix() . $formatted_value . '' . $this->patientIdentifierType->getDisplayIdentifierSuffix();
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PatientIdentifier the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function defaultScope()
    {
        if ($this->getDefaultScopeDisabled()) {
            return [];
        }

        return ['condition' => 'patient_identifier_not_deleted.deleted = 0', 'alias' => 'patient_identifier_not_deleted'];
    }

    public function beforeValidate()
    {
        $this->value = str_replace(' ', '', $this->value);
        return parent::beforeValidate();
    }

    protected function beforeSave()
    {
        $unique_row_string = "";
        $unique_row_string .= $this->patient_id . '-' . $this->patient_identifier_type_id . '-';
        if($this->deleted === 0) {
            $unique_row_string .= 'ACTIVE';
        } else {
            $unique_row_string .= $this->source_info . '-' . $this->value;
        }

        $this->unique_row_string = $unique_row_string;

        return parent::beforeSave();
    }
}
