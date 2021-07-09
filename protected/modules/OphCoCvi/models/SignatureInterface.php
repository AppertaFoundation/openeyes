<?php

namespace OEModule\OphCoCvi\models;

interface SignatureInterface
{
    /** @return bool */
    public function checkSignature();
}