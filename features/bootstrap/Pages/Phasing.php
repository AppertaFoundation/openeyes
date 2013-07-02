<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class Phasing extends Page
{
    public static $phasingInstrumentRight = "//select[@id='element_ophciphasing_intraocularpressure_right_instrument_id']";
    public static $phasingDilationRight = "//input[@id='element_ophciphasing_intraocularpressure_right_dilated_1']";
    public static $phasingPressureRight = "//input[@id='intraocularpressure_reading_0_value']";
    public static $phasingCommentsRight = "//textarea[@id='element_ophciphasing_intraocularpressure_right_comments']";
    public static $phasingInstrumentLeft = "//select[@id='element_ophciphasing_intraocularpressure_left_instrument_id']";
    public static $phasingDilationLeft = "//input[@id='element_ophciphasing_intraocularpressure_left_dilated_1']";
    public static $phasingPressureLeft = "//input[@id='intraocularpressure_reading_1_value']";
    public static $phasingCommentsLeft = "//textarea[@id='element_ophciphasing_intraocularpressure_left_comments']";
}