<?php

namespace OEModule\OphCiExamination\models;

use OEModule\OphDrPGDPSD\models\Element_DrugAdministration_record;

class Element_OphCiExamination_DrugAdministration_record extends Element_DrugAdministration_record
{
    public static function getUsageType()
    {
        return "OphCiExamination";
    }

    public static function getUsageSubtype()
    {
        return "DrugAdministration";
    }
}
