<?php
class OperationNote extends OpenEyesPage
{
    protected $path = "/site/OphTrOperationbooking/Default/create?patient_id={parentId}";

    protected $elements = array(

        'emergencyBooking' => array('xpath' => "//*[@value='emergency']"),
        'createOperationNote' => array('xpath' => "//*[@form='operation-note-select']"),

    );

    public function emergencyBooking ()
    {
        $this->getElement('emergencyBooking')->check();
    }

    public function createOperationNote ()
    {
        $this->getElement('createOperationNote')->click();
    }

}