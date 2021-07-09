<?php

namespace OEModule\OphCoCvi\widgets;

class ConsultantSignatureElementWidget extends \ConsultantSignatureElementWidget
{
    public function getInfoMessage()
    {
        return "Please note the CVI is only valid if signed by a Consultant";
    }
}