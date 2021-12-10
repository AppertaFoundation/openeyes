<?php

class BreakGlassModel extends CFormModel
{
    public $reason;
    public $longreason;

    public function rules()
    {
        return array(
            array('reason, longreason', 'validateResponse'),
        );
    }

    public function validateResponse($attribute, $params) {
        if (!array_key_exists($this->reason, $this->getReasons())) {
            $this->addError('reason', 'Valid reason must be selected');
        }
        if ($this->reason == 'Other' && !$this->longreason) {
            $this->addError('longreason', 'Reason for access must be provided');
        }
    }

    public function getReasons() {
        return array (
            'CommunityOptometrist' => 'I am the Patient\'s community optometrist',
            'EmergencyCare' => 'I am providing emergency care',
            'OnBehalfOfAnotherArea' => 'I am working on behalf of another area',
            'AdministrativeSupport' => 'I am providing administrative support',
            'Other' => 'Another reason not on this list'
        );
    }
}
