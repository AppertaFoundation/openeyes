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
    /**
     * @var int|string|null $type
     */
    public $type;
    protected array $options = array(
        'value_type' => 'string_search',
        'operations' => array(
            array('label' => 'IS', 'id' => '='),
            array('label' => 'IS NOT', 'id' => '!='),
        ),
        'accepted_template_strings' => array(
            array('id' => 'institution', 'label' => 'ID for Current Institution', 'target' => 'type')
        ),
    );

    protected string $label_ = 'Identifier Number';

    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'patient_identifier';
        $this->options['option_data'] = array(
            array(
                'id' => 'type',
                'field' => 'type',
                'options' => array_map(
                    static function ($item, $key) {
                        return array('id' => $key, 'label' => $item);
                    },
                    $this->getAllTypes(),
                    array_keys($this->getAllTypes())
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
                'type' => 'Identifier Type'
            )
        );
    }

    /**
     * @param string $attribute
     * @return mixed|void
     * @throws CException
     */
    public function getValueForAttribute(string $attribute)
    {
        if (in_array($attribute, $this->attributeNames(), true)) {
            if ($attribute === 'type') {
                if (preg_match(Yii::app()->getModule('OECaseSearch')->getConfigParam('template_string_regex'), $this->$attribute)) {
                    $value = str_replace(array('{', '}'), '', $this->$attribute);
                    $accepted_strings = array_column($this->options['accepted_template_strings'], 'label', 'id');
                    if (array_key_exists($value, $accepted_strings)) {
                        return ('Identifier Type - [' . $accepted_strings[$value] . ']') ?? 'Unknown';
                    }
                }
                if ($this->$attribute) {
                    $type = PatientIdentifierType::model()->findByPk($this->$attribute);
                    return 'Identifier Type - ' . ($type->short_title ?? 'Unknown');
                }

                return 'All identifier types';
            }
            return parent::getValueForAttribute($attribute);
        }
        return parent::getValueForAttribute($attribute);
    }

    /**
     * Override this function if the parameter subclass has extra validation rules.
     * If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
     * @return array The validation rules for the parameter.
     */
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('value', 'required'),
            array('value, type', 'safe')
        ));
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @return string The constructed query string.
     */
    public function query(): string
    {
        $op = $this->operation;
        return "SELECT DISTINCT p.patient_id
FROM patient_identifier p
WHERE (:p_type_{$this->id} IS NULL OR p.patient_identifier_type_id {$op} :p_type_{$this->id})
  AND p.value {$op} :p_id_number_{$this->id}";
    }

    public static function getCommonItemsForTerm(string $term) : array
    {
        $patients = Yii::app()->db->createCommand(
            "SELECT DISTINCT p.value FROM patient_identifier p
WHERE p.value LIKE :term
ORDER BY p.value LIMIT " . self::_AUTOCOMPLETE_LIMIT,

        )
            ->bindValues(array('term' => "%$term%"))
            ->queryAll();
        return array_map(
            static function ($patient) {
                return array('id' => $patient['value'], 'label' => $patient['value']);
            },
            $patients
        );
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     * @throws Exception
     */
    public function bindValues(): array
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        if (str_replace(array('{', '}'), '', $this->type) === 'institution') {
            $code_type = PatientIdentifierType::model()->findByAttributes(array('institution_id' => Institution::model()->getCurrent()->id))->id ?? null;
            return array(
                "p_id_number_$this->id" => $this->value,
                "p_type_$this->id" => $code_type,
            );
        }
        return array(
            "p_id_number_$this->id" => $this->value,
            "p_type_$this->id" => $this->type,
        );
    }

    /**
     * @inherit
     */
    public function getAuditData() : string
    {
        if (str_replace(array('{', '}'), '', $this->type) === 'institution') {
            $typeStr = '([' . $this->options['accepted_template_strings'][0]['label'] . '])';
        } else {
            $type = PatientIdentifierType::model()->findByPk($this->type);
            $typeStr = $type ? '(' . $type->short_title . ')' : '(All identifiers)';
        }

        return "$this->name: = $this->value $typeStr";
    }

    /**
     * @return array contains all identifier types
     */
    public function getAllTypes(): array
    {
        $all_types = PatientIdentifierType::model()->findAll();
        $types = array();
        foreach ($all_types as $type) {
            $types[$type->id] = $type->short_title;
        }
        return $types;
    }

    public function saveSearch() : array
    {
        return array_merge(
            parent::saveSearch(),
            array(
                'type' => $this->type,
            )
        );
    }
}
