<?php

use OEModule\OphCiExamination\models\PastSurgery_Operation;

/**
 * Class PreviousProceduresParameter
 */
class PreviousProceduresParameter extends CaseSearchParameter implements DBProviderInterface
{
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

    public function getLabel()
    {
        // This is a human-readable value, so feel free to change this as required.
        return 'Previous Procedure';
    }

    public static function getCommonItemsForTerm($term)
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
        $criteria->addNotInCondition('operation', $options);
        $past_ops = PastSurgery_Operation::model()->findAll($criteria);

        foreach ($past_ops as $op) {
            $options[] = array('id' => $op->id, 'label' => $op->operation);
        }
        return $options;
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @param $searchProvider DBProvider The search provider. This is used to determine whether or not the search provider is using SQL syntax.
     * @return string The constructed query string.
     *
     */
    public function query($searchProvider)
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
            AND proc.term = :p_p_value_$this->id
            UNION
            SELECT pa.id
            FROM patient pa
            JOIN episode ep ON ep.patient_id = pa.id
            JOIN event e on ep.id = e.episode_id
            JOIN et_ophciexamination_pastsurgery eop2 on e.id = eop2.event_id
            JOIN ophciexamination_pastsurgery_op o3 on eop2.id = o3.element_id
               AND o3.operation = :p_p_value_$this->id";

        if ($this->operation) {
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

    public function saveSearch()
    {
        return array_merge(
            parent::saveSearch(),
            array(
                'textValue' => $this->value,
            )
        );
    }

    public function getDisplayString()
    {
        $op = 'IS';
        if ($this->operation) {
            $op = 'IS NOT';
        }

        return "Previous procedure $op = $this->value";
    }
}
