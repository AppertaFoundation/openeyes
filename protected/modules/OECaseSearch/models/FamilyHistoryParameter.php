<?php

/**
 * Class FamilyHistoryParameter
 */

use OEModule\OphCiExamination\models\FamilyHistoryCondition;
use OEModule\OphCiExamination\models\FamilyHistoryRelative;
use OEModule\OphCiExamination\models\FamilyHistorySide;

class FamilyHistoryParameter extends CaseSearchParameter implements DBProviderInterface
{
    /**
     * @var int|null $relative
     */
    public ?int $relative = null;

    /**
     * @var int|null $side
     */
    public ?int $side = null;

    /**
     * @var int|null $condition
     */
    public ?int $condition = null;

    protected string $label_ = 'Family History';

    protected array $options = array(
        'value_type' => 'multi_select',
    );

    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'family_history';
        $this->operation = 'IN';

        $this->options['operations'][0] = array('label' => 'INCLUDES', 'id' => 'IN');
        $this->options['operations'][1] = array('label' => 'DOES NOT INCLUDE', 'id' => 'NOT IN');

        $relatives = FamilyHistoryRelative::model()->findAll();
        $sides = FamilyHistorySide::model()->findAll();
        $conditions = FamilyHistoryCondition::model()->findAll();

        $this->options['option_data'] = array(
            array(
                'id' => 'family-side',
                'field' => 'side',
                'options' => array_merge(
                    array(
                        array('id' => null, 'label' => 'Any', 'selected' => true,)
                    ),
                    array_map(
                        static function ($item) {
                            return array('id' => $item->id, 'label' => $item->name);
                        },
                        $sides
                    ),
                ),
            ),
            array(
                'id' => 'family-relative',
                'field' => 'relative',
                'options' => array_merge(
                    array(
                        array('id' => null, 'label' => 'Any', 'selected' => true,)
                    ),
                    array_map(
                        static function ($item) {
                            return array('id' => $item->id, 'label' => $item->name);
                        },
                        $relatives
                    ),
                ),
            ),
            array(
                'id' => 'family-condition',
                'field' => 'condition',
                'options' => array_merge(
                    array(
                        array('id' => null, 'label' => 'Any', 'selected' => true,)
                    ),
                    array_map(
                        static function ($item) {
                            return array('id' => $item->id, 'label' => $item->name);
                        },
                        $conditions
                    )
                ),
            ),
        );
    }

    public function getValueForAttribute(string $attribute)
    {
        if (in_array($attribute, $this->attributeNames(), true)) {
            switch ($attribute) {
                case 'relative':
                    return $this->$attribute
                        ? (FamilyHistoryRelative::model()->findByPk($this->$attribute))->name
                        : 'Any relative';
                case 'side':
                    return $this->$attribute
                        ? (FamilyHistorySide::model()->findByPk($this->$attribute))->name
                        : 'Any side of family';
                case 'condition':
                    return $this->$attribute
                        ? 'has ' . (FamilyHistoryCondition::model()->findByPk($this->$attribute))->name
                        : 'Any condition';
                default:
                    return parent::getValueForAttribute($attribute);
            }
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
        return array_merge(
            parent::rules(),
            array(
                array('relative, side, condition', 'safe'),
            )
        );
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @return string The constructed query string.
     */
    public function query(): string
    {
        $query_side = '';
        $query_relative = '';
        $query_condition = '';

        if ($this->side !== '') {
            $query_side = "AND (:f_h_side_$this->id IS NULL OR fh.side_id = :f_h_side_$this->id)";
        }
        if ($this->relative !== '') {
            $query_relative = " AND (:f_h_relative_$this->id IS NULL OR fh.relative_id = :f_h_relative_$this->id)";
        }
        if ($this->condition !== '') {
            $query_condition = " AND (:f_h_condition_$this->id IS NULL OR fh.condition_id = :f_h_condition_$this->id)";
        }

        $baseQuery = "
SELECT DISTINCT p.id 
FROM patient p 
JOIN patient_family_history fh
  ON fh.patient_id = p.id
WHERE 1=1 {$query_side} {$query_relative} {$query_condition}";

        if ($this->operation === 'IN') {
            return $baseQuery;
        }
        return "SELECT id FROM patient p WHERE id NOT IN ({$baseQuery})";
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues(): array
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        // Matched bind parameter numbers to those on the query - CERA-538
        $binds = array();
        if ($this->relative !== '' || $this->relative !== null) {
            $binds["f_h_relative_$this->id"] = $this->relative;
        }
        if ($this->side !== '' || $this->side !== null) {
            $binds["f_h_side_$this->id"] = $this->side;
        }
        if ($this->condition !== '' || $this->condition !== null) {
            $binds["f_h_condition_$this->id"] = $this->condition;
        }
        return $binds;
    }

    /**
     * @inherit
     */
    public function getAuditData() : string
    {
        $side = $this->side ?: 'Any side';
        $relative = $this->relative ?: 'any relative';
        $condition = $this->condition ?: 'any condition';
        $op = null;

        if ($this->operation === 'IN') {
            $op = '=';
        } elseif ($this->operation === 'NOT IN') {
            $op = '!=';
        }
        return "$this->name: $side $relative $op \"$condition\"";
    }

    public function saveSearch() : array
    {
        return array_merge(
            parent::saveSearch(),
            array(
                'relative' => $this->relative,
                'side' => $this->side,
                'condition' => $this->condition,
            )
        );
    }
}
