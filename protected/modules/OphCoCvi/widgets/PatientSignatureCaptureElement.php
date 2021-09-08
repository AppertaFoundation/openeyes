<?php

namespace OEModule\OphCoCvi\widgets;

class PatientSignatureCaptureElement extends \PatientSignatureCaptureElement
{
    public function init()
    {
        return parent::init();
    }

    protected $additional_edit_mode_templates = [
        "application.modules.OphCoCvi.views.default._form_consent_consignees",
    ];

    protected $additional_view_mode_templates = [
        "application.modules.OphCoCvi.views.default._view_consent_consignees",
    ];

    public function getInViewMode()
    {
        return $this->inViewMode();
    }

    protected function getView()
    {
        $viewFile = $this->inEditMode() ? "application.modules.OphCoCvi.widgets.views.PatientSignatureCaptureElement_event_edit" : "application.widgets.views.PatientSignatureCaptureElement_event_view";
        return $viewFile;
    }
}
