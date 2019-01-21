<?php

/**
 * Class PatientMedicationParameter
 */
class PatientMedicationParameter extends CaseSearchParameter implements DBProviderInterface
{
    public $textValue;

    /**
     * CaseSearchParameter constructor. This overrides the parent constructor so that the name can be immediately set.
     * @param string $scenario
     */
    public function __construct($scenario = '')
    {
        parent::__construct($scenario);
        $this->name = 'medication';
    }

    public function getLabel()
    {
        // This is a human-readable value, so feel free to change this as required.
        return 'Medication';
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
        return array_merge(parent::rules(), array(
                array('textValue', 'required'),
            )
        );
    }

    public function renderParameter($id)
    {
        // Place screen-rendering code here.
        $ops = array(
            'LIKE' => 'Has taken ',
            'NOT LIKE' => 'Has not taken',
        );
        ?>

        <div class="row field-row">
            <div class="large-2 column">
                <?php echo CHtml::label($this->getLabel(), false); ?>
            </div>
            <div class="large-3 column">
                <?php echo CHtml::activeDropDownList($this, "[$id]operation", $ops, array('prompt' => 'Select One...')); ?>
                <?php echo CHtml::error($this, "[$id]operation"); ?>
            </div>

            <div class="large-7 column">
                <?php
                $html = Yii::app()->controller->widget('zii.widgets.jui.CJuiAutoComplete', array(
                    'name' => $this->name . $this->id,
                    'model' => $this,
                    'attribute' => "[$id]textValue",
                    'source' => Yii::app()->controller->createUrl('AutoComplete/commonMedicines'),
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
     * @throws CHttpException
     */
    public function query($searchProvider)
    {
        switch ($this->operation) {
            case 'LIKE':
                $op = 'LIKE';
                $wildcard = '%';

                return "
SELECT p.id 
FROM patient p 
LEFT JOIN medication m 
  ON m.patient_id = p.id 
LEFT JOIN drug d 
  ON d.id = m.drug_id
LEFT JOIN medication_drug md
  ON md.id = m.medication_drug_id
WHERE d.name $op '$wildcard' || :p_m_value_$this->id || '$wildcard'
  OR md.name $op '$wildcard' || :p_m_value_$this->id || '$wildcard'";
                break;
            case 'NOT LIKE':
                $op = 'NOT LIKE';
                $wildcard = '%';

                return "
SELECT p.id 
FROM patient p 
LEFT JOIN medication m 
  ON m.patient_id = p.id 
LEFT JOIN drug d 
  ON d.id = m.drug_id
LEFT JOIN medication_drug md
  ON md.id = m.medication_drug_id
WHERE d.name $op '$wildcard' || :p_m_value_$this->id || '$wildcard'
  OR md.name $op '$wildcard' || :p_m_value_$this->id || '$wildcard'
  OR m.id IS NULL";
                break;
            default:
                throw new CHttpException(400, 'Invalid operator specified.');
                break;
        }
    }

    /**
     * Get the list of bind values for use in the SQL query.
     * @return array An array of bind values. The keys correspond to the named binds in the query string.
     */
    public function bindValues()
    {
        // Construct your list of bind values here. Use the format "bind" => "value".
        return array(
            "p_m_value_$this->id" => $this->textValue,
        );
    }

    /**
     * @inherit
     */
    public function getAuditData()
    {
        return "$this->name: $this->operation \"$this->textValue\"";
    }
}
