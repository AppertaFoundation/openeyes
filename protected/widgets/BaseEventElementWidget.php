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
     * @var string of the module name.
     */
    public static $moduleName;
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

    protected function validateMode($mode)
    {
        return in_array($this->mode,
            array(static::$PATIENT_SUMMARY_MODE, static::$EVENT_VIEW_MODE, static::$EVENT_EDIT_MODE));
    }

    /**
     * @throws CHttpException
     */
    public function init()
    {
        if (!isset($this->mode)) {
            $this->mode = static::$PATIENT_SUMMARY_MODE;
        }
        if (!$this->validateMode($this->mode)) {
            throw new \CHttpException('invalid mode value for ' . static::class);
        }
        $this->initialiseElement();

        parent::init();
    }

    /**
     * return the latest Element
     */
    protected function getLatestElement()
    {
        return $this->getApp()->moduleAPI->get(static::$moduleName)->getLatestElement(get_class($this->getNewElement()), $this->patient);
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

            if ($this->mode != static::$EVENT_EDIT_MODE) {
                // must be in a view mode so just load the most recent
                $this->element = $this->getNewElement()->getMostRecentForPatient($this->patient);
            } else {
                $this->element = $this->getNewElement();
            }
        }

        if ($this->element && $this->element->getIsNewRecord()) {
            // when new we want to always set to default so we can track changes
            // but if this element already exists then we don't want to override
            // it with the tip data
            $this->element->setDefaultOptions($this->patient);
        }

        if ($this->data) {
            // we set the element to the provided data
            $this->updateElementFromData($this->element, $this->data);
        }
    }

    /**
     * Should be overridden in specific widget classes
     *
     * @return BaseEventTypeElement
     */
    protected function getNewElement()
    {
        return new BaseEventTypeElement();
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

    /**
     * Determine whether this widget should support editing
     *
     * @return bool
     */
    public function canEdit()
    {
        // Note, the decision was taken during implementation to disable editing at the patient level
        // but because it was implemented for Family History already, it made sense to simply disable
        // the behaviour in case it was required later. Turning this on will require further work on those
        // elements that were not developed prior to this decision. Use Family History as a model.
        if ($this->mode === static::$PATIENT_SUMMARY_MODE &&
            !$this->getApp()->params['allow_patient_summary_clinic_changes']) {
            return  false;
        }
        return $this->getApp()->user->checkAccess('OprnCreateEvent', array($this->controller->firm));
    }

    /**
     * Wrapper to the controller method to do standard access checking
     *
     * @param $operation
     * @return mixed
     */
    public function checkAccess($operation)
    {
        return $this->controller->checkAccess($operation);
    }

    /**
     * @return mixed
     */
    public function summary()
    {
        $element = $this->element;
        return (string) $element;
    }
}