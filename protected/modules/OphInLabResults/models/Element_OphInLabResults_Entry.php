<?php

class Element_OphInLabResults_Entry extends Element_OphInLabResults_ResultTimedNumeric
{
    protected $htmlOptions = array(
        'time' => array('type' => 'time'),
        'result' => array('type' => 'number', 'step' => 0.1, 'min' => 0.1, 'max' => 50),
    );

    /**
     * @return string
     */
    public function __toString()
    {
        $string = '';
        if ($this->event) {
            if ($this->result) {
                $string = $this->result . ' (at ' . $this->time . ', ' . date_create_from_format('Y-m-d H:i:s', $this->event->event_date)->format('d/m/Y') . ')';
            }

            if ($this->comment) {
                $string .= ' - ' . $this->comment;
            }
        }

        return $string;
    }

    public function getPrint_view()
    {
        return 'print_'.$this->getDefaultView();
    }

    public function getViewTitle()
    {
        return $this->resultType->type;
    }

    public function getFormTitle()
    {
        return $this->resultType->type;
    }
}
