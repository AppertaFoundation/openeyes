<?php

class Element_OphInLabResults_Inr extends Element_OphInLabResults_ResultTimedNumeric
{
    protected $htmlOptions = array(
        'time' => array('type' => 'time'),
        'result' => array('type' => 'number', 'step' => 0.1, 'min' => 0.1, 'max' => 50),
    );

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $parentRules = parent::rules();

        $rules = array(
            array('result', 'numerical', 'min' => 0.1, 'max' => 50),
        );

        $parentRules = $this->overrideRules($parentRules, $rules);

        return $parentRules;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $string = '';
        if($this->event) {
            if ($this->result) {
                $string = $this->result . ' (at ' . $this->time . ', ' . date_create_from_format('Y-m-d H:i:s', $this->event->event_date)->format('d/m/Y') . ')';
            }

            if ($this->comment) {
                $string .= ' - ' . $this->comment;
            }
        }

        return $string;
    }
}
