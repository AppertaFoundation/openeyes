<?php

/**
 * Class PatientAllergyParameter
 */
class PatientAllergyParameter extends CaseSearchParameter implements DBProviderInterface
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
        $this->name = 'allergy';
        $this->operation = '=';
    }

    public function getLabel()
    {
        return 'Patient Allergy';
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
        $ops = array(
            '=' => 'Is allergic to',
            '!=' => 'Is not allergic to',
        );
        ?>

        <div class="flex-layout flex-left js-case-search-param">
          <div class="parameter-option">

              <?= $this->getDisplayTitle() ?>
          </div>
          <div style="padding-right: 15px;">
              <?php echo CHtml::activeDropDownList($this, "[$id]operation", $ops, array('prompt' => 'Select One...')); ?>
              <?php echo CHtml::error($this, "[$id]operation"); ?>
          </div>

            <div class="">
                <?php
                $html = Yii::app()->controller->widget(
                    'zii.widgets.jui.CJuiAutoComplete',
                    array(
                        'name' => $this->name . $this->id,
                        'model' => $this,
                        'attribute' => "[$id]textValue",
                        'source' => Yii::app()->controller->createUrl('AutoComplete/commonAllergies'),
                        'options' => array(
                            'minLength' => 2,
                        ),
                    ),
                    true
                );
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
        $query = "SELECT DISTINCT p.id 
FROM patient p 
LEFT JOIN patient_allergy_assignment paa
  ON paa.patient_id = p.id
LEFT JOIN allergy a
  ON a.id = paa.allergy_id
WHERE a.name = :p_al_textValue_$this->id";
        switch ($this->operation) {
            case '=':
                return $query;
                break;
            case '!=':
                return "SELECT DISTINCT p1.id
FROM patient p1
WHERE p1.id NOT IN (
  $query
)";
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
            "p_al_textValue_$this->id" => $this->textValue,
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
