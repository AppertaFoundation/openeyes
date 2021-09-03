<?php
/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2019
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class PatientIdentifierParameter
 */
class PatientIdentifierParameter extends CaseSearchParameter implements DBProviderInterface
{
    public $code;
    protected $options = array(
        'value_type' => 'string_search',
    );

    protected $label_ = 'Identifier Number';

    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     * @throws CException
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'patient_identifier';
        $this->operation = '='; // Remove if more operations are added.
        $this->options['option_data'] = array(
            array(
                'id' => 'code',
                'field' => 'code',
                'options' => array_map(
                    static function ($item, $key) {
                        return array('id' => $key, 'label' => $item);
                    },
                    $this->getAllCodes(),
                    array_keys($this->getAllCodes())
                ),
            ),
        );
    }

    /**
     * Attribute labels for display purposes.
     * @return array Attribute key/value pairs.
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            array(
                'code' => 'Code'
            )
        );
    }

    /**
     * @param $attribute
     * @return string|null
     * @throws CException
     */
    public function getValueForAttribute($attribute)
    {
        if (in_array($attribute, $this->attributeNames(), true)) {
            switch ($attribute) {
                case 'code':
                    return 'Code - ' . $this->$attribute;
                    break;
                default:
                    return parent::getValueForAttribute($attribute);
            }
        }
        return parent::getValueForAttribute($attribute);
    }

    /**
     * Override this function if the parameter subclass has extra validation rules. If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
     * @return array The validation rules for the parameter.
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('value, code', 'required'),
            array('value, code', 'safe')
        ));
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @return string The constructed query string.
     */
    public function query()
    {
        $op = '=';
        return "SELECT DISTINCT p.patient_id 
FROM patient_identifier p
WHERE p.code $op :p_code_$this->id AND p.value $op :p_id_number_$this->id";
    }

    public static function getCommonItemsForTerm($term)
    {
        $patients = PatientIdentifier::model()->findAllBySql(
            "SELECT p.* FROM patient_identifier p
WHERE p.value LIKE :term
ORDER BY p.value, p.code LIMIT " . self::_AUTOCOMPLETE_LIMIT,
            array('term' => "%$term%")
        );
        $values = array();
        foreach ($patients as $patient) {
            $values[] = array('id' => $patient->value, 'label' => $patient->value);
        }
        return $values;
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        return array(
            "p_id_number_$this->id" => $this->value,
            "p_code_$this->id" => $this->code,
        );
    }

    /**
     * @inherit
     */
    public function getAuditData()
    {
        return "$this->name: = $this->code $this->value";
    }

    /**
     * @return array contains all identifier codes
     * @throws CException
     */
    public function getAllCodes()
    {
        $all_codes = Yii::app()->db->createCommand('SELECT DISTINCT code FROM patient_identifier')->queryAll();
        $codes = array();
        foreach ($all_codes as $code) {
            $codes[$code['code']] = $code['code'];
        }
        return $codes;
    }

    public function saveSearch()
    {
        return array_merge(
            parent::saveSearch(),
            array(
                'code' => $this->code,
            )
        );
    }
}
