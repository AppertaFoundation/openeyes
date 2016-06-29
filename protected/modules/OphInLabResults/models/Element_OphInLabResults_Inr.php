<?php

class Element_OphInLabResults_Inr extends Element_OphInLabResults_ResultTimedNumeric
{
    protected $htmlOptions = array(
        'time' => array('type' => 'time'),
        'result' => array('type' => 'number', 'step' => 0.1, 'min' => 0.1, 'max' => 50),
    );

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $parentRules = parent::rules();
        
        $rules = array(
            array('result', 'numerical', 'min' => 0.1, 'max' => 50)
        );

        $parentRules = $this->overrideRules($parentRules, $rules);

        return $parentRules;
    }
    
}