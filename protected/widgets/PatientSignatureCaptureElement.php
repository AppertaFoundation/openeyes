<?php

class PatientSignatureCaptureElement extends BaseSignatureCaptureElement
{
    protected function getView()
    {
        $viewPath = "application.widgets.views";
        switch ($this->mode) {
            case self::$EVENT_EDIT_MODE:
                $viewFile = "PatientSignatureCaptureElement_event_edit";
                break;

            case self::$EVENT_SIGN_MODE:
                $viewFile = "PatientSignatureCaptureElement_event_sign";
                break;

            case self::$EVENT_VIEW_MODE:
                $viewFile = "PatientSignatureCaptureElement_event_view";
                break;
        }
        return $viewPath.".".$viewFile;
    }
}