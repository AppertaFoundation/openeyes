<?php

use OEModule\OphCiExamination\models\PastSurgery_Operation;

/**
 * Class PreviousProceduresParameter
 */
class PreviousProceduresParameter extends CaseSearchParameter implements DBProviderInterface
{
    protected ?string $label_ = 'Previous Procedure';

    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'previous_procedures';
        $this->operation = 'LIKE';
    }

    public function rules()
    {
        return array_merge(
            parent::rules(),
            array(
                array('value', 'required'),
                array('value', 'safe'),
            )
        );
    }

    /**
     * @param string $attribute
     * @return string|null
     * @throws CException
     */
    public function getValueForAttribute(string $attribute)
    {
        if (in_array($attribute, $this->attributeNames(), true)) {
            switch ($attribute) {
                case 'value':
                    $op = Procedure::model()->findByPk($this->$attribute);
                    if (!$op) {
                        $op = PastSurgery_Operation::model()->findByPk($this->$attribute);
                        return $op->operation;
                    }
                    return $op->term;
                default:
                    return parent::getValueForAttribute($attribute);
            }
        }
        return parent::getValueForAttribute($attribute);
    }

    public static function getCommonItemsForTerm(string $term)
    {
        $criteria = new CDbCriteria();
        $criteria->limit = 15;
        $criteria->compare('term', $term, true);
        $procedures = Procedure::model()->findAll($criteria);

        $options = array();
        foreach ($procedures as $procedure) {
            $options[] = array('id' => $procedure->id, 'label' => $procedure->term);
        }

        $criteria = new CDbCriteria();
        $criteria->limit = 15;
        $criteria->compare('operation', $term, true);
        $criteria->addNotInCondition('operation', array_column($options, 'id'));
        $past_ops = PastSurgery_Operation::model()->findAll($criteria);

        foreach ($past_ops as $op) {
            $options[] = array('id' => $op->id, 'label' => $op->operation);
        }
        return $options;
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @return string The constructed query string.
     *
     */
    public function query()
    {
        $query = "
            SELECT pa.id
            FROM patient pa
            JOIN episode ep ON ep.patient_id = pa.id
            JOIN event ev ON ep.id = ev.episode_id
            JOIN et_ophtroperationnote_procedurelist eop ON ev.id = eop.booking_event_id
            JOIN et_ophtroperationbooking_operation o ON ev.id = o.event_id
              AND o.status_id = (SELECT id FROM ophtroperationbooking_operation_status WHERE name = 'Completed')
            JOIN ophtroperationnote_procedurelist_procedure_assignment op ON eop.id = op.procedurelist_id
            JOIN proc ON op.proc_id = proc.id
            AND proc.id = :p_p_value_$this->id
            UNION
            SELECT pa.id
            FROM patient pa
            JOIN episode ep ON ep.patient_id = pa.id
            JOIN event e on ep.id = e.episode_id
            JOIN et_ophciexamination_pastsurgery eop2 on e.id = eop2.event_id
            JOIN ophciexamination_pastsurgery_op o3 on eop2.id = o3.element_id
               AND o3.id = :p_p_value_$this->id";

        if ($this->operation === '!=') {
            $query = "
                SELECT outer_pat.id
                FROM patient outer_pat 
                WHERE outer_pat.id NOT IN (
                  $query
                )";
        }

        return $query;
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        return array(
            "p_p_value_$this->id" => $this->value,
        );
    }

    public function getAuditData()
    {
        $str =  parent::getAuditData();
        $proc = Procedure::model()->findByPk($this->value);
        return $str . ": $this->operation $proc->term";
    }
}
