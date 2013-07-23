<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Phasing extends Page
{
    public  $phasingInstrumentRight = "//select[@id='element_ophciphasing_intraocularpressure_right_instrument_id']";
    public  $phasingDilationRight = "//input[@id='element_ophciphasing_intraocularpressure_right_dilated_1']";
    public  $phasingPressureRight = "//input[@id='intraocularpressure_reading_0_value']";
    public  $phasingCommentsRight = "//textarea[@id='element_ophciphasing_intraocularpressure_right_comments']";
    public  $phasingInstrumentLeft = "//select[@id='element_ophciphasing_intraocularpressure_left_instrument_id']";
    public  $phasingDilationLeft = "//input[@id='element_ophciphasing_intraocularpressure_left_dilated_1']";
    public  $phasingPressureLeft = "//input[@id='intraocularpressure_reading_1_value']";
    public  $phasingCommentsLeft = "//textarea[@id='element_ophciphasing_intraocularpressure_left_comments']";
}