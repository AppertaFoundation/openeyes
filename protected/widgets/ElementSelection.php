<?php


class ElementSelection extends DropDownList
{
    public $relatedElements;
    public $hidden = false;

    public function run()
    {
        $list = array();

        foreach ($this->relatedElements as $element) {
            $list[$element->id]  = $element->getElementType()->name . ' ' . Helper::convertMySQL2NHS($element->event->event_date);
        }

        $this->data = $list;

        $this->render('DropDownList');
    }
}
