<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Phasing extends Page
{
    protected $elements = array(
        'phasingInstrumentRight' => array('xpath' => "//select[@id='element_ophciphasing_intraocularpressure_right_instrument_id']"),
        'phasingDilationRight' => array('xpath' => "//input[@id='element_ophciphasing_intraocularpressure_right_dilated_1']"),
        'phasingPressureRight' => array('xpath' => "//input[@id='intraocularpressure_reading_0_value']"),
        'phasingCommentsRight' => array('xpath' => "//textarea[@id='element_ophciphasing_intraocularpressure_right_comments']"),
        'phasingInstrumentLeft' => array('xpath' => "//select[@id='element_ophciphasing_intraocularpressure_left_instrument_id']"),
        'phasingDilationLeft' => array('xpath' => "//input[@id='element_ophciphasing_intraocularpressure_left_dilated_1']"),
        'phasingPressureLeft' => array('xpath' => "//input[@id='intraocularpressure_reading_1_value']"),
        'phasingCommentsLeft' => array('xpath' => "//textarea[@id='element_ophciphasing_intraocularpressure_left_comments']"),
    );

}