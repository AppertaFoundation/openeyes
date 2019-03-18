<?php

/**
 * Class PreviousProceduresParameter
 */
class PreviousProceduresParameter extends CaseSearchParameter implements DBProviderInterface
{
    /**
     * @var string $textValue
     */
    public $textValue;

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
        return 'Previous Procedures';
    }

    /**
     * Override this function for any new attributes added to the subclass. Ensure that you invoke the parent function first to obtain and augment the initial list of attribute names.
     * @return array An array of attribute names.
     */
    public function attributeNames()
    {
        return array_merge(parent::attributeNames(), array(
                'textValue',
            )
        );
    }

    /**
     * Override this function if the parameter subclass has extra validation rules. If doing so, ensure you invoke the parent function first to obtain the initial list of rules.
     * @return array The validation rules for the parameter.
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            array(
                array('textValue', 'required')
            )
        );
    }

    public function renderParameter($id)
    {
        $ops = array(
            'HAS_HAD' => 'Has had a',
            'HAS_NOT_HAD' => 'Has not had a',
        );
        ?>

        <div class="flex-layout flex-left">
            <?= $this->getDisplayTitle() ?>
            <div style="padding-right: 15px;">
                <?php echo CHtml::activeDropDownList($this, "[$id]operation", $ops, array('prompt' => 'Select One...')); ?>
                <?php echo CHtml::error($this, "[$id]operation"); ?>
            </div>

            <div>
                <?php
                $html = Yii::app()->controller->widget('zii.widgets.jui.CJuiAutoComplete', array(
                    'name' => $this->name . $this->id,
                    'model' => $this,
                    'attribute' => "[$id]textValue",
                    'source' => Yii::app()->controller->createUrl('AutoComplete/commonProcedures'),
                    'options' => array(
                        'minLength' => 2,
                    ),
                ), true);
                Yii::app()->clientScript->render($html);
                echo $html;
                ?>
                <?php echo CHtml::error($this, "[$id]textValue"); ?>
            </div>
        </div>
        <?php
    }

    /**
     * Generate a SQL fragment representing the subquery of a FROM condition.
     * @param $searchProvider DBProvider The search provider. This is used to determine whether or not the search provider is using SQL syntax.
     * @return string The constructed query string.
     *
     * @throws CHttpException when operator is invalid
     */
    public function query($searchProvider)
    {
        if (!in_array($this->operation, array('HAS_HAD', 'HAS_NOT_HAD'))){
            throw new CHttpException(400, 'Invalid operator specified.');
        }

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

        if ($this->operation === 'HAS_NOT_HAD'){
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
            "p_p_value_$this->id" => $this->textValue,
        );
    }
}
