<?php
/**
 * Class InstitutionParameter
 */
class InstitutionParameter extends CaseSearchParameter implements DBProviderInterface
{
    protected string $label_ = 'Institution';
    protected array $options = array(
        'value_type' => 'string_search',
        'operations' => array(
            array('label' => 'IS', 'id' => '='),
            array('label' => 'IS NOT', 'id' => '!='),
        ),
        'accepted_template_strings' => array(
            array('id' => 'institution', 'label' => 'Current Institution')
        ),
    );

    /**
    * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
    * @param string $scenario
    */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'institution';
    }

    /**
     * @param string $attribute
     * @return mixed|void
     */
    public function getValueForAttribute(string $attribute)
    {
        // Parse the template string and check if it's a template string that can be processed by this parameter.
        // If so, return the display string for display from the list of enabled template strings.
        if (preg_match(Yii::app()->getModule('OECaseSearch')->getConfigParam('template_string_regex'), $this->$attribute)) {
            $value = str_replace(array('{', '}'), '', $this->$attribute);
            $accepted_strings = array_column($this->options['accepted_template_strings'], 'label', 'id');
            if (array_key_exists($value, $accepted_strings)) {
                return ('[' . $accepted_strings[$value] . ']') ?? 'Unknown';
            }
        }
        $institution = Institution::model()->findByPk($this->$attribute);
        return $institution->name ?? 'Any Institution';
    }

    public static function getCommonItemsForTerm(string $term) : array
    {
        // Add customisation here
        $matches = Yii::app()->db->createCommand()
            ->select('id, name')
            ->from('institution')
            ->where("LOWER(name) LIKE LOWER(CONCAT(:name, '%'))")
            ->bindValues(array(':name' => $term))
            ->queryAll();
        return array_map(
            static function ($item) {
                return array('id' => $item['id'], 'label' => $item['name']);
            },
            $matches
        );
    }

    /**
    * Generate a SQL fragment representing the subquery of a FROM condition.
    * @return string The constructed query string.
    */
    public function query() : string
    {
        // Using the patient_identifier table to determine if a patient has a record with a specific institution.
        $query = "SELECT DISTINCT p_{$this->id}.id
FROM patient p_{$this->id}
JOIN patient_identifier pi_{$this->id} ON pi_{$this->id}.patient_id = p_{$this->id}.id
JOIN patient_identifier_type pit_{$this->id} ON pit_{$this->id}.id = pi_{$this->id}.patient_identifier_type_id
WHERE :i_value_{$this->id} IS NULL OR pit_{$this->id}.institution_id = :i_value_{$this->id}";
        if ($this->operation !== '=') {
            return "SELECT p_outer.id FROM patient p_outer WHERE p_outer.id NOT IN (
                {$query}
            )";
        }
        return $query;
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     * @throws Exception
     */
    public function bindValues() : array
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        if (str_replace(array('{', '}'), '', $this->value) === 'institution') {
            return array(
                ":i_value_{$this->id}" => Institution::model()->getCurrent()->id,
            );
        }
        return array(
            ":i_value_{$this->id}" => $this->value,
        );
    }

    public function getAuditData() : string
    {
        $value = null;
        $str = null;
        if (preg_match(Yii::app()->getModule('OECaseSearch')->getConfigParam('template_string_regex'), $this->value)) {
            $value = str_replace(array('{', '}'), '', $this->value);
            $accepted_strings = array_column($this->options['accepted_template_strings'], 'label', 'id');
            if (array_key_exists($value, $accepted_strings)) {
                $str = ('[' . $accepted_strings[$value] . ']') ?? 'Unknown';
            }
        } else {
            $value = Institution::model()->findByPk($this->value);
            $str = $value->name ?? 'Any';
        }

        return "Institution: {$this->operation} {$str}";
    }
}
