<?php

abstract class BaseSignatureCaptureElement extends BaseEventElementWidget
{
    protected $additional_edit_mode_templates = array();
    protected $additional_view_mode_templates = array();

    public static $EVENT_SIGN_MODE = 256;

    /**
     * @param $mode
     * @return bool
     */
    protected function validateMode($mode)
    {
        return in_array(
            $mode,
            array(
                static::$PATIENT_SUMMARY_MODE, static::$PATIENT_POPUP_MODE,
                static::$EVENT_VIEW_MODE, static::$EVENT_PRINT_MODE,
                static::$EVENT_EDIT_MODE, static::$DATA_MODE,
                static::$PATIENT_LANDING_PAGE_MODE, static::$EVENT_SIGN_MODE
            ),
            true
        );
    }

    public function init()
    {
        return parent::init();
    }
}