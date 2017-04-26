<?php

/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */
class BaseEventElementWidget extends CWidget
{
    public static $PATIENT_SUMMARY_MODE = 1;
    public static $EVENT_VIEW_MODE = 2;
    public static $EVENT_EDIT_MODE = 4;

    /**
     * @var \Patient
     */
    public $patient;
    /**
     * @var BaseEventTypeElement
     */
    public $element;

    public $mode;
    public $view_file;
    public $data;
    public $form;

    /**
     * @var CApplication
     */
    protected $app;

    /**
     * Wrapper function to return the Yii app instance
     *
     * @return CApplication
     */
    protected function getApp()
    {
        if (!$this->app) {
            $this->app = \Yii::app();
        }
        return $this->app;
    }

    /**
     * @throws CHttpException
     */
    public function init()
    {
        if (!in_array($this->mode,
            array(static::$PATIENT_SUMMARY_MODE, static::$EVENT_VIEW_MODE, static::$EVENT_EDIT_MODE))
        ) {
            throw new \CHttpException('invalid mode value for ' . static::class);
        }
        $this->initialiseElement();

        parent::init();
    }

    /**
     * @throws CHttpException
     */
    protected function initialiseElement()
    {
        if (!$this->element) {
            if (!$this->patient) {
                throw new \CHttpException('Patient required to initialise ' . static::class . ' with no element.');
            }
            $this->element = new FamilyHistoryElement();
            $this->element->setDefaultOptions($this->patient);
        }
        elseif ($this->data) {
            $this->updateElementFromData($this->element, $this->data);
        }
    }

    /**
     * Basic setting of element attributes from provided data. Should be overridden to
     * handle any complex attributes
     *
     * @param $element
     * @param $data
     */
    protected function updateElementFromData($element, $data)
    {
        $element->attributes = $data;
    }

    /**
     * @param string $filename
     * @return string
     */
    public function getJsPublishedPath($filename = null)
    {
        $class = new \ReflectionClass($this);
        $path = dirname($class->getFileName()) . DIRECTORY_SEPARATOR . 'js/';
        if ($filename) {
            $path .= $filename;
        }
        return $this->getApp()->getAssetManager()->publish($path);
    }
}