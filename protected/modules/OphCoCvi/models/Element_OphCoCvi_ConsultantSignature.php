<?php

namespace OEModule\OphCoCvi\models;

use OEModule\OphCoCvi\widgets\ConsultantSignatureElementWidget;

class Element_OphCoCvi_ConsultantSignature extends \Element_ConsultantSignature implements SignatureInterface
{
    public function getWidgetClass()
    {
        return ConsultantSignatureElementWidget::class;
    }

    public function getElementTypeName()
    {
        return "Consultant signature";
    }

    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            array ('pin' => 'Please enter your PIN')
        );
    }

    public function getElementsForCVIpdf()
    {
        return [
            'Opth_Name' => $this->signed_by->title.' '. $this->signed_by->first_name . ' '.$this->signed_by->last_name
        ];
    }

    public function checkSignature()
    {
        return $this->isSigned();
    }
}
