<?php

class ConsultantSignatureElementWidget extends BaseEventElementWidget
{
    /**
     * @return bool
     */
    public function isSigningAllowed()
    {
        return true;
    }

    /**
     * @return string|null
     */
    public function getInfoMessage()
    {
        return null;
    }

    protected function getView()
    {
        $viewPath = "application.widgets.views";
        $viewFile = "ConsultantSignatureElement_event_edit";
        return $viewPath.".".$viewFile;
    }
}
