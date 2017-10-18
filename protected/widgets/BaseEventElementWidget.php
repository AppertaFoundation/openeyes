<?php

/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */
class BaseEventElementWidget extends CWidget
{
    public static $PATIENT_SUMMARY_MODE = 1;
    public static $PATIENT_POPUP_MODE = 2;
    public static $EVENT_VIEW_MODE = 4;
    public static $EVENT_PRINT_MODE = 8;
    public static $EVENT_EDIT_MODE = 16;
    public static $EPISODE_SUMMARY_MODE = 32;
    public static $DATA_MODE = 64;

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
    /**
     * @var \Firm
     */
    public $firm;
    
    public $mode;
    public $view_file;
    public $data;
    public $form;
    public $popupListSeparator = '<br />';

    public $notattip_edit_warning = 'application.widgets.views.BaseEventElement_edit_nottip';
    public $notattip_view_warning = 'application.widgets.views.BaseEventElement_view_nottip';
    protected $print_view;

    public static function latestForPatient(Patient $patient)
    {
        $widget = new static();
        $widget->patient = $patient;
        $widget->mode = static::$PATIENT_SUMMARY_MODE;
        $widget->init();
        return $widget->element;
    }

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

    public function getFirm()
    {
        if (!isset($this->firm)) {
            $firm_id = $this->getApp()->session->get('selected_firm_id');
            $this->firm = $firm_id ? Firm::model()->findByPk($firm_id) : null;
        }
        return $this->firm;
    }

    /**
     * @param $mode
     * @return bool
     */
    protected function validateMode($mode)
    {
        return in_array($mode,
            array(static::$PATIENT_SUMMARY_MODE, static::$PATIENT_POPUP_MODE,
                static::$EVENT_VIEW_MODE, static::$EVENT_PRINT_MODE,
                static::$EVENT_EDIT_MODE, static::$DATA_MODE), true);
    }

    /**
     * @return bool
     */
    protected function inEditMode() {
        return $this->mode === static::$EVENT_EDIT_MODE;
    }

    /**
     * @return bool
     */
    protected function showEditTipWarning() {
        return $this->inEditMode();
    }

    /**
     * @return bool
     */
    protected function inViewMode() {
        return in_array($this->mode, array(static::$PATIENT_SUMMARY_MODE, static::$EVENT_VIEW_MODE), true);
    }

    /**
     * @return bool
     */
    protected function showViewTipWarning() {
        return $this->inViewMode();
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
            $this->setElementFromDefaults();
        }

        if ($this->data) {
            // we set the element to the provided data
            $this->updateElementFromData($this->element, $this->data);
        }
        $this->element->widget = $this;
    }

    protected function isAtTip()
    {
        return $this->element->isAtTip();
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
     * Basic setting of element attributes with default behaviour (typically
     * loading from the tip). Should be overridden for any pre/post processing
     * requirements.
     */
    protected function setElementFromDefaults()
    {
        $this->element->setDefaultOptions($this->patient);
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
     * @var string base path to assets for element widget
     */
    private $base_published_path;

    /**
     * @return string
     */
    protected function getBasePublishedPath()
    {
        if (!$this->base_published_path) {
            $class = new \ReflectionClass($this);
            $this->base_published_path = dirname($class->getFileName());
        }
        return $this->base_published_path;
    }

    /**
     * @param $path
     * @param $filename
     * @param boolean $core - get path for a core asset or not.
     * @return mixed
     */
    protected function getPublishedPath($path, $filename, $core = false)
    {
        $root = $core ?
            $this->getApp()->getBasePath() . DIRECTORY_SEPARATOR . 'assets'
            : $this->getBasePublishedPath();
        // remove any null entries prior to implosion
        $elements = array_filter(
            array($root, $path, $filename),
            function($el) {
                return $el !== null;
            }
        );

        return $this->getApp()->getAssetManager()->publish(
            implode(DIRECTORY_SEPARATOR, $elements)
        );
    }

    /**
     * @param string $filename
     * @param boolean $core - js is from core or not
     * @return string
     */
    public function getJsPublishedPath($filename = null, $core = false)
    {
        return $this->getPublishedPath('js', $filename, $core);
    }

    /**
     * @param string $filename
     * @return mixed
     */
    public function getCssPublishedPath($filename = null)
    {
        return $this->getPublishedPath('../assets/css', $filename);
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
    public function popupList()
    {
        $element = $this->element;
        return (string) $element;
    }

    /**
     * Determine the view file to use
     */
    protected function getView()
    {
        if ($this->view_file) {
            // manually overridden/set
            return $this->view_file;
        }

        // quick way to get the base class name
        $short_name = substr(strrchr(get_class($this), '\\'),1);
        switch ($this->mode) {
            case static::$EVENT_VIEW_MODE:
                return $short_name . '_event_view';
                break;
            case static::$EVENT_PRINT_MODE:
                // defaults to the standard view unless widget defines a print view
                return $this->print_view ? : $short_name . '_event_view';
                break;
            case static::$EVENT_EDIT_MODE:
                return $short_name . '_event_edit';
                break;
            case static::$EPISODE_SUMMARY_MODE:
                return $short_name . '_episodesummary';
                break;
            case static::$DATA_MODE:
                throw new \SystemException('No view to render when ' . static::class . ' in DATA_MODE');
                break;
            default:
                return $short_name . '_patient_mode';
        }
    }

    /**
     * @return array
     */
    public function getViewData()
    {
        return array(
            'element' => $this->element,
            'form' => $this->form
        );
    }



    /**
     * @return string
     */
    public function run()
    {
        // TODO: refactor this out for consistent rendering
        if ($this->mode === static::$PATIENT_POPUP_MODE) {
            return $this->popupList();
        }

        return $this->renderWarnings() . $this->render($this->getView(), $this->getViewData());
    }

    /**
     * @return string
     */
    public function renderWarnings()
    {
        if (!$this->isAtTip()) {
            if ($this->showEditTipWarning()) {
                return $this->render($this->notattip_edit_warning, array('element' => $this->element));
            }
            if ($this->showViewTipWarning()) {
                return $this->render($this->notattip_view_warning, array('element' => $this->element));
            }
        }

    }
}
