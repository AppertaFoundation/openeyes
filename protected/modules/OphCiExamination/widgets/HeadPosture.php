<?php

namespace OEModule\OphCiExamination\widgets;

use OEModule\OphCiExamination\models\HeadPosture as HeadPostureElement;

class HeadPosture extends \BaseEventElementWidget
{
    /**
     * @return HeadPostureElement
     */
    protected function getNewElement()
    {
        return new HeadPostureElement();
    }
}