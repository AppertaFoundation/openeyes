<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Class BaseEventTypeController.
 *
 * BaseEventTypeController is the base controller for modules managing events within OpenEyes.
 *
 * It implements a standardised design pattern to provide the general CRUD interface for module events. The controller
 * is designed to be stateful. When an action is called, the state of the controller is determined from the POST and GET
 * attributes of the request. Properties on the controller are populated through a series of methods, and the response
 * is rendered based on these values, and returned to the user. The rationale behind this is that each of the methods
 * provide discrete hooks which can be overridden in module controllers to redefine what the controller properties
 * should be set to.
 *
 * The primary property of the controller to be manipulated is the {@link open_elements} which defines the elements of
 * the event to be displayed in whatever action is being performed.
 *
 * An abstract class in all but name, it should be used for all event based modules. Specific methods can be implemented
 * in module level controllers that will be called automatically by this base controller. Specifically setting defaults
 * on elements and setting complex attributes on individual elements can be handled in specific methods, as defined by
 * <ul>
 * <li>{@link setElementDefaultOptions}</li>
 * <li>{@link setElementComplexAttributesFromData}</li>
 * <li>{@link saveElementComplexAttributesFromData}</li>
 * </ul>
 *
 * It's worth noting that at the moment there is no class for Events at the module level. As a result, the controller
 * tends to contain certain business logic that should really be part of the event. Such behaviour should be written in
 * a way that it can be easily extracted into a separate class. The intention in the future is that this would be abstracted
 * into (at a minimum) a helper class, or ideally into an actual event class that would contain all business logic for
 * manipulating the event and its elements.
 *
 * Furthermore no $_POST, $_GET or session data should be utilised within the element models. Data should be extracted
 * by controllers and passed to methods on the element models. In the future, models may be instantiated in different
 * context where these globals would not be available.
 */
class BaseEventTypeController extends BaseModuleController
{
    const ACTION_TYPE_CREATE = 'Create';
    const ACTION_TYPE_VIEW = 'View';
    const ACTION_TYPE_PRINT = 'Print';
    const ACTION_TYPE_EDIT = 'Edit';
    const ACTION_TYPE_DELETE = 'Delete';
    const ACTION_TYPE_REQUESTDELETE = 'RequestDelete';
    const ACTION_TYPE_FORM = 'Form';    // AJAX actions that are used during create and update but don't actually modify data themselves

    private $unique_code_elements = array(
        array('event' => 'OphTrOperationnote', 'element' => array('Element_OphTrOperationnote_Cataract')),
        array('event' => 'OphCoCvi', 'element' => array('Element_OphCoCvi_EventInfo')),
    );

    private static $base_action_types = array(
        'create' => self::ACTION_TYPE_CREATE,
        'view' => self::ACTION_TYPE_VIEW,
        'elementForm' => self::ACTION_TYPE_FORM,
        'viewPreviousElements' => self::ACTION_TYPE_FORM,
        'print' => self::ACTION_TYPE_PRINT,
        'PDFprint' => self::ACTION_TYPE_PRINT,
        'update' => self::ACTION_TYPE_EDIT,
        'delete' => self::ACTION_TYPE_DELETE,
        'requestDeletion' => self::ACTION_TYPE_REQUESTDELETE,
        'eventImage' => self::ACTION_TYPE_VIEW,
        'printCopy' => self::ACTION_TYPE_PRINT,
        'savePDFprint' => self::ACTION_TYPE_PRINT,
        'createImage' => self::ACTION_TYPE_VIEW,
        'EDTagSearch' => self::ACTION_TYPE_FORM,
        'renderEventImage' => self::ACTION_TYPE_VIEW,
        'removed' => self::ACTION_TYPE_VIEW,
        'saveTemplate' => self::ACTION_TYPE_FORM,
        'updateTemplate' => self::ACTION_TYPE_FORM,
    );

    /**
     * Override for custom actions.
     *
     * @var array
     */
    protected static $action_types = array();

    /* @var Patient */
    public $patient;
    /* @var Site */
    public $site;
    public ?Event $event = null;
    public ?EventTemplate $template = null;
    public $editable = true;
    public $editing;
    private $title;
    public $episode;
    public $moduleStateCssClass = '';
    public $event_tabs = array();
    public $event_actions = array();
    public $successUri = 'default/view/';
    // String to set an issue when an event is created
    public $eventIssueCreate;
    // defines additional variables to be available in view templates
    public $extraViewProperties = array();
    public $layout = '//layouts/events_and_episodes';
    private $action_type_map;
    private $episodes = array();
    public $renderPatientPanel = true;
    protected $render_optional_elements = true;

    protected $open_elements;
    public $dont_redirect = false;
    public $pdf_print_suffix = null;
    public $pdf_print_documents = 1;
    public $pdf_print_html = null;
    public $attachment_print_title = null;
    public $print_args = null;

    protected ?EventSubType $event_subtype = null;

    /**
     * Values to change per event
     *
     * @var float $resolution_multiplier how much to 'zoom in' on the pdf when changing to png
     * @var int $image_width width of preview image in pixels
     * @var int $compression_quality from 1 (lowest) to 100 (highest)
     */
    public $resolution_multiplier = 1;
    public $image_width = 800;
    public $compression_quality = 50;

    /**
     * @var int $element_tiles_wide The number of tiles that can be rendered in a single row
     */
    protected $element_tiles_wide = 3;

    /**
     * Set to false if the event list should remain on the sidebar when creating/editing the event
     *
     * @var bool
     */
    protected $show_element_sidebar = false;

    /**
     * Set to true if the index search bar should appear in the header when creating/editing the event
     *
     * @var bool
     */
    protected $show_index_search = false;

    /**
     * Set to true if the manage element should appear on the sidebar
     *
     * @var bool
     */
    protected $show_manage_elements = false;

    /**
     * Additional errors outside of the scope of the Elements
     * An example usage of this is for CreateEventsAfterEventSavedBehavior
     * where we need to validate inputs not belonging to any elements
     * and there would be no point to add validation to every Controller we want to use.
     * At the time of writing $external_errors is only used in OpNote DefaultController actionCreate
     * and added in this controllers actionCreate
     *
     * @var array
     */
    public array $external_errors = [];
    protected $has_conflict = false;

    public function hasConflict()
    {
        return $this->has_conflict;
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'WorklistBehavior' => ['class' => 'application.behaviors.WorklistBehavior',],
            'CreateEventBehavior' => ['class' => 'application.behaviors.CreateEventControllerBehavior',]
        ]);
    }

    public function getPageTitle()
    {
        $action_type = ucfirst($this->getAction()->getId());
        return ((in_array($action_type, ['Update', 'Create']) && (string)SettingMetadata::model()->getSetting(
            'use_short_page_titles'
        ) == "on") ?
                'Edit' : $action_type) .
            ($this->event_type ? ' ' . $this->event_type->name : '') .
            ((string)SettingMetadata::model()->getSetting('use_short_page_titles') != "on" ?
                ($this->patient ? ' - ' . $this->patient->last_name . ', ' . $this->patient->first_name : '') .
                ' - OE' : '');
    }

    public function getTitle()
    {
        if (isset($this->event) && $this->event->firstEventSubtypeItem) {
            return $this->event->firstEventSubtypeItem->eventSubtype->display_name;
        }
        if (isset($this->title)) {
            return $this->title;
        }
        if (isset($this->event_type)) {
            return $this->event_type->name;
        }

        return '';
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function init()
    {
        $this->action_type_map = array();
        foreach (self::$base_action_types as $action => $type) {
            $this->action_type_map[strtolower($action)] = $type;
        }
        foreach (static::$action_types as $action => $type) {
            $this->action_type_map[strtolower($action)] = $type;
        }

        return parent::init();
    }

    public function accessRules()
    {
        // Allow logged in users - the main authorisation check happens later in verifyActionAccess
        return array(array('allow', 'users' => array('@')));
    }

    /**
     * Wrapper around the episode property on this controller - current_episode is used in patient layouts.
     *
     * @return Episode
     */
    public function getCurrent_episode()
    {
        return $this->episode;
    }

    /**
     * Return an ACTION_TYPE_ constant representing the type of an action for authorisation purposes.
     *
     * @param string $action
     *
     * @return string
     * @throws Exception
     *
     */
    public function getActionType($action)
    {
        if (!isset($this->action_type_map[strtolower($action)])) {
            throw new Exception("Action '{$action}' has no type associated with it");
        }

        return $this->action_type_map[strtolower($action)];
    }

    /**
     * @param $action
     * @return int
     */
    public function getElementWidgetMode($action)
    {
        return [
                static::ACTION_TYPE_CREATE => BaseEventElementWidget::$EVENT_EDIT_MODE,
                static::ACTION_TYPE_EDIT => BaseEventElementWidget::$EVENT_EDIT_MODE,
                static::ACTION_TYPE_FORM => BaseEventElementWidget::$EVENT_EDIT_MODE,
                static::ACTION_TYPE_PRINT => BaseEventElementWidget::$EVENT_PRINT_MODE,
            ][$this->getActionType($action)]
            ?? BaseEventElementWidget::$EVENT_VIEW_MODE;
    }

    /**
     * Sets the patient object on the controller.
     *
     * @param $patient_id
     *
     * @throws CHttpException
     */
    protected function setPatient($patient_id)
    {
        if (!$this->patient = Patient::model()->findByPk($patient_id)) {
            throw new CHttpException(404, 'Invalid patient_id.');
        }
    }

    /**
     * Abstraction of getting the elements for the event being controlled to allow more complex overrides (such as workflow)
     * where required.
     *
     * This should be overridden if the standard elements for the event are affected by the controller state.
     *
     * @return BaseEventTypeElement[]
     */
    protected function getEventElements()
    {
        if ($this->event && !$this->event->isNewRecord) {
            return $this->event->getElements();
        } else {
            return $this->event_type->getDefaultElements();
        }
    }

    /**
     * based on the current state of the controller, sets the open_elements property, which is the array of relevant
     * open elements for the controller.
     */
    protected function setOpenElementsFromCurrentEvent($action)
    {
        $this->open_elements = $this->getEventElements();
        $this->setElementOptions($action);
    }

    /**
     * Renders the metadata of the event with the standard template.
     *
     * @param string $view
     */
    public function renderEventMetadata($view = '//patient/event_metadata')
    {
        $this->renderPartial($view);
    }

    /**
     * Get the open elements for the event that are not children.
     *
     * @return array
     */
    public function getElements($action = 'edit')
    {
        $elements = array();
        if (is_array($this->open_elements)) {
            foreach ($this->open_elements as $element) {
                if ($element->getElementType()) {
                    $elements[] = $element;
                }
            }
        }

        return $elements;
    }

    /**
     * @return ElementType[]
     */
    protected function getAllElementTypes()
    {
        return $this->event_type->getAllElementTypes();
    }

    /**
     * @param array $remove_list
     * @return string
     */
    public function getElementTree($remove_list = array())
    {
        $element_types_tree = array();
        foreach ($this->event_type->getAllElementGroups() as $element_group) {
            $struct = array(
                'name' => $element_group->name,
                'element_group_id' => $element_group->id,
                'display_order' => $element_group->display_order,
                'children' => array(),
            );
            $children = $this->event_type->getElementTypesForGroup($element_group->id);
            if (count($remove_list)) {
                $children = array_filter($children, function ($child) use ($remove_list) {
                    return !in_array($child->class_name, $remove_list);
                });
            }
            // Single child elements should load the child for them
            if (count($children) === 1) {
                $element_type = reset($children);
                $struct['id'] = $element_type->id;
                $struct['class_name'] = CHtml::modelName($element_type->class_name);
                $struct['display_order'] = CHtml::modelName($element_type->display_order);
                $element_types_tree[] = $struct;
            } elseif (count($children) > 0) {
                foreach ($children as $element_type) {
                    $struct['children'][] = array(
                        'name' => $element_type->name,
                        'id' => $element_type->id,
                        'display_order' => $element_type->display_order,
                        'class_name' => CHtml::modelName($element_type->class_name),
                    );
                }
                $element_types_tree[] = $struct;
            }
        }
        return json_encode($element_types_tree);
    }

    /**
     * Get the optional elements for the current module's event type (that are not children).
     *
     * @return BaseEventTypeElement[] $elements
     */
    public function getOptionalElements()
    {
        $open_et = array();
        foreach ($this->open_elements as $open) {
            $open_et[] = get_class($open);
        }

        $optional = array();
        foreach ($this->event_type->getAllElementTypes() as $element_type) {
            if (
                !in_array($element_type->class_name, $open_et) &&
                class_exists($element_type->class_name)
            ) {
                $optional[] = $element_type->getInstance();
            }
        }

        return $optional;
    }

    /**
     * Override to use $action_types.
     *
     * @param string $action
     *
     * @return bool
     */
    public function isPrintAction($action)
    {
        return self::getActionType($action) == self::ACTION_TYPE_PRINT;
    }

    /**
     * Setup base css/js etc requirements for the eventual action render.
     *
     * @param $action
     *
     * @return bool
     *
     * @throws CHttpException
     *
     * @see parent::beforeAction($action)
     */
    protected function beforeAction($action)
    {
        // Automatic file inclusion unless it's an ajax call
        if ($this->assetPath && !Yii::app()->getRequest()->getIsAjaxRequest()) {
            if (!$this->isPrintAction($action->id)) {
                // nested elements behaviour
                //TODO: possibly put this into standard js library for events
                Yii::app()->getClientScript()->registerScript(
                    'nestedElementJS',
                    'var moduleName = "' . $this->getModule()->name . '";',
                    CClientScript::POS_HEAD
                );
                Yii::app()->assetManager->registerScriptFile('js/nested_elements.js');
                Yii::app()->assetManager->registerScriptFile("js/OpenEyes.UI.InlinePreviousElements.js");
                // disable buttons when clicking on save/save_draft/save_print
                Yii::app()->assetManager->getClientScript()->registerScript(
                    'disableSaveAfterClick',
                    '
                      $(document).on("click", "#et_save, #et_save_footer, #et_save_draft, #et_save_draft_footer, #et_save_print, #et_save_print_footer, #et_save_print_form, #et_save_print_form_footer", function () {
                          disableButtons();
                      });
                ',
                    CClientScript::POS_HEAD
                );
            }
        }

        $this->setInstitutionFromSession();
        $this->setFirmFromSession();

        if (!isset($this->firm)) {
            // No firm selected, reject
            throw new CHttpException(403, 'You are not authorised to view this page without selecting a firm.');
        }

        if ($_REQUEST) {
            $_REQUEST = $this->sanitizeInput($_REQUEST);
        }

        if ($_POST) {
            $_POST = $this->sanitizeInput($_POST);
        }

        if ($_GET) {
            $_GET = $this->sanitizeInput($_GET);
        }

        $this->initAction($action->id);

        $this->verifyActionAccess($action);

        // prevent the user try to perform any actions to the deleted events other than removed action
        if ($this->event && $this->event->deleted && $action->id !== 'removed') {
            $url = $this->event->getEventViewPath();
            $this->redirect($url);
        }

        return parent::beforeAction($action);
    }

    /**
     * Redirect to the patient episodes when the controller determines the action cannot be carried out.
     */
    protected function redirectToPatientLandingPage()
    {
        $this->redirect((new CoreAPI())->generatePatientLandingPageLink($this->patient));
    }

    /**
     * set the defaults on the given BaseEventTypeElement.
     *
     * Looks for a methods based on the class name of the element:
     * setElementDefaultOptions_[element class name]
     *
     * This method is passed the element and action, which allows for controller methods to manipulate the default
     * values of the element (if the controller state is required for this)
     *
     * @param BaseEventTypeElement $element
     * @param string $action
     */
    protected function setElementDefaultOptions($element, $action)
    {
        if ($action == 'create') {
            $element->setDefaultOptions($this->patient);
        } elseif ($action == 'update') {
            $element->setUpdateOptions();
        }

        $el_method = 'setElementDefaultOptions_' . Helper::getNSShortname($element);
        if (method_exists($this, $el_method)) {
            $this->$el_method($element, $action);
        }
    }

    /**
     * Set the default values on each of the open elements.
     *
     * @param string $action
     */
    protected function setElementOptions($action)
    {
        foreach ($this->open_elements as $element) {
            $this->setElementDefaultOptions($element, $action);
        }
    }

    protected function getPrevious($element_type, $exclude_event_id = null)
    {
        if ($api = $this->getApp()->moduleAPI->get($this->getModule()->name)) {
            return array_filter(
                $api->getElements($element_type->class_name, $this->patient, false),
                function ($el) use ($exclude_event_id) {
                    return $el->event_id != $exclude_event_id;
                }
            );
        } else {
            return array();
        }
    }

    /**
     * Are there one or more previous instances of an element?
     *
     * @param ElementType $element_type
     * @param int $exclude_event_id
     *
     * @return bool
     */
    public function hasPrevious($element_type, $exclude_event_id = null)
    {
        return count($this->getPrevious($element_type, $exclude_event_id)) > 0;
    }

    /**
     * Can an element can be copied from a previous version.
     *
     * @param BaseEventTypeElement $element
     *
     * @return bool
     */
    public function canCopy($element)
    {
        return $element->canCopy() && $this->hasPrevious($element->getElementType(), $element->event_id);
    }

    /**
     * Can we view the previous version of the element.
     *
     * @param BaseEventTypeElement $element
     *
     * @return bool
     */
    public function canViewPrevious($element)
    {
        return $element->canViewPrevious() && $this->hasPrevious($element->getElementType(), $element->event_id);
    }

    /**
     * Is this a required element?
     *
     * @param BaseEventTypeElement $element
     *
     * @return bool
     */
    public function isRequired(BaseEventTypeElement $element)
    {
        return $element->isRequired();
    }

    /**
     * Is this element required in the UI? (Prevents the user from being able
     * to remove the element.).
     *
     * @param BaseEventTypeElement $element
     *
     * @return bool
     */
    public function isRequiredInUI(BaseEventTypeElement $element)
    {
        return $element->isRequiredInUI();
    }

    /**
     * Is this element to be hidden in the UI? (Prevents the elements from
     * being displayed on page load.).
     *
     * @param BaseEventTypeElement $element
     *
     * @return bool
     */
    public function isHiddenInUI(BaseEventTypeElement $element)
    {
        return $element->isHiddenInUI();
    }

    /**
     * Initialise an element of $element_type for returning as an individual form. If the $previous_id is provided,
     * then the default values of the element will be overridden with the properties of the previous intance of the
     * element. Similarly, $additional allows specific values to be set on the element.
     *
     * Abstracted to allow overrides in specific module controllers
     *
     * @param ElementType $element_type
     * @param int $previous_id
     * @param array $additional - additional attributes for the element
     *
     * @return \BaseEventTypeElement
     */
    protected function getElementForElementForm($element_type, $previous_id = 0, $additional = [])
    {
        $element_class = $element_type->class_name;
        $element = $element_type->getInstance();
        $this->setElementDefaultOptions($element, 'create');

        if ($previous_id && $element->canCopy()) {
            $previous_element = $element_class::model()->findByPk($previous_id);
            $element->loadFromExisting($previous_element);
        }

        foreach (array_keys($additional) as $add) {
            if ($element->isAttributeSafe($add)) {
                $element->$add = $additional[$add];
            }
        }

        return $element;
    }

    /**
     * Runs initialisation of the controller based on the action. Looks for a method name of.
     *
     * initAction[$action]
     *
     * and calls it.
     *
     * @param string $action
     */
    protected function initAction($action)
    {
        $init_method = 'initAction' . ucfirst($action);
        if (method_exists($this, $init_method)) {
            $this->$init_method();
        }
    }

    /**
     * Initialise the controller prior to a create action.
     *
     * @throws CHttpException
     */
    protected function initActionCreate()
    {
        $this->moduleStateCssClass = 'edit';

        $this->setPatient($_REQUEST['patient_id']);

        if (!$this->episode = $this->getEpisode()) {
            $this->redirectToPatientLandingPage();
        }

        // we instantiate an event object for use with validation rules that are dependent
        // on episode and patient status
        $this->event = new Event();
        $this->event->episode_id = $this->episode->id;
        $this->event->event_type_id = $this->event_type->id;
        $this->event->last_modified_user_id = $this->event->created_user_id = Yii::app()->user->id;

        if (isset($_GET['template_id'])) {
            $this->template = EventTemplate::model()->findByPk($_GET['template_id']);
        }
    }

    /**
     * Intialise controller property based off the event id.
     *
     * @param $id
     *
     * @throws CHttpException
     */
    protected function initWithEventId($id)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('event_type_id = ?');
        $criteria->params = array($this->event_type->id);
        if (!$id || !$this->event = Event::model()->findByPk($id, $criteria)) {
            throw new CHttpException(404, 'Invalid event id.');
        }

        $this->patient = $this->event->episode->patient;
        $this->episode = $this->event->episode;
        $this->successUri = $this->successUri . $this->event->id;
    }

    /**
     * Sets the the css state.
     */
    protected function initActionView()
    {
        $this->readInEventImageSettings();
        $this->moduleStateCssClass = 'view';
        $this->initWithEventId(@$_GET['id']);
    }

    /**
     * initialise the controller prior to event update action.
     *
     * @throws CHttpException
     */
    protected function initActionUpdate()
    {
        $this->moduleStateCssClass = 'edit';

        $this->initWithEventId(@$_GET['id']);

        if (isset($_GET['template_id'])) {
            $this->template = EventTemplate::model()->findByPk($_GET['template_id']);
        }
    }

    /**
     * initialise the controller with the event id.
     */
    protected function initActionDelete()
    {
        $this->initWithEventId(@$_GET['id']);

        //on soft delete we call the afterSoftDelete method
        $this->event->getEventHandlers('onAfterSoftDelete')->add(array($this, 'afterSoftDelete'));
    }

    /**
     * @param CAction $action
     */
    protected function verifyActionAccess(CAction $action)
    {
        $actionType = $this->getActionType($action->id);
        $method = "check{$actionType}Access";

        if (!method_exists($this, $method)) {
            throw new Exception("No access check method found for action type '{$actionType}'");
        }

        if (!$this->$method()) {
            switch ($actionType) {
                case self::ACTION_TYPE_VIEW:
                case self::ACTION_TYPE_CREATE:
                case self::ACTION_TYPE_EDIT:
                    $this->redirectToPatientLandingPage();
                    break;
                default:
                    throw new CHttpException(403);
            }
        }
    }

    /**
     * @return bool
     */
    public function checkCreateAccess()
    {
        return $this->checkAccess('OprnCreateEvent', $this->firm, $this->episode, $this->event_type);
    }

    /**
     * @return bool
     */
    public function checkViewAccess()
    {
        return $this->checkAccess('OprnViewClinical');
    }

    /**
     * @return bool
     */
    public function checkPrintAccess()
    {
        return $this->checkAccess('OprnPrint');
    }

    /**
     * @return bool
     */
    public function checkEditAccess()
    {
        return $this->checkAccess('OprnEditEvent', $this->event);
    }

    /**
     * @return bool
     */
    public function checkDeleteAccess()
    {
        return $this->checkAccess('OprnDeleteEvent', Yii::app()->session['user'], $this->event);
    }

    /**
     * @return bool
     */
    public function checkRequestDeleteAccess()
    {
        return $this->checkAccess('OprnRequestEventDeletion', $this->event);
    }

    /**
     * @return bool
     */
    public function checkFormAccess()
    {
        return $this->checkAccess('OprnViewClinical');
    }

    /**
     * @return bool
     */
    public function checkAdminAccess()
    {
        return $this->checkAccess('admin');
    }

    /**
     * Carries out the base create action.
     *
     * @throws CHttpException
     * @throws Exception
     */
    public function actionCreate()
    {
        $this->event->firm_id = $this->selectedFirmId;
        $errors = [];
        if (!empty($_POST)) {
            // form has been submitted
            if (isset($_POST['cancel'])) {
                $this->redirectToPatientLandingPage();
            }

            // set and validate
            $errors = $this->setAndValidateElementsFromData($_POST);
            if ($this->external_errors) {
                $errors = array_merge($errors, $this->external_errors);
            }

            // creation
            if (empty($errors)) {
                $transaction = Yii::app()->db->beginInternalTransaction();

                try {
                    $success = $this->saveEvent($_POST);

                    if ($success) {
                        //TODO: should this be in the save event as pass through?
                        if ($this->eventIssueCreate) {
                            $this->event->addIssue($this->eventIssueCreate);
                        }

                        $this->updateEventStep();

                        //TODO: should not be passing event?
                        $this->afterCreateElements($this->event);

                        $this->logActivity('created event.');

                        $this->event->audit('event', 'create');

                        Yii::app()->user->setFlash('success', "{$this->getTitle()} created.");

                        Yii::app()->event->dispatch('event_created', ['event' => $this->event, 'action' => 'create']);

                        $transaction->commit();

                        /*
                         * After event saved and transaction is commited
                         * here we can generate additional events with their own transactions
                         */
                        $this->afterCreateEvent($this->event);

                        unset(
                            Yii::app()->session['active_worklist_patient_id'],
                            Yii::app()->session['active_step_id'],
                            Yii::app()->session['active_step_state_data']
                        );

                        if ($this->event->parent_id) {
                            $this->redirect(
                                Yii::app()->createUrl(
                                    '/' . $this->event->parent->eventType->class_name . '/default/view/' . $this->event->parent_id
                                )
                            );
                        } else {
                            if (!empty($this->event->eventType->template_class_name)) {
                                if (!empty($this->template)) {
                                    $existing_template_data = json_decode($this->template->getDetailRecord()->template_data, true);
                                    $template_status = $this->event->getTemplateUpdateStatusForEvent($existing_template_data);

                                    if ($template_status !== 'UNNEEDED') {
                                        $this->redirect(array($this->successUri . $this->event->id . '?template=' . $template_status));
                                    } else {
                                        $this->redirect(array($this->successUri . $this->event->id));
                                    }
                                } else {
                                    $this->redirect(array($this->successUri . $this->event->id . '?template=' . EventTemplate::UPDATE_CREATE_ONLY));
                                }
                            } else {
                                $this->redirect(array($this->successUri . $this->event->id));
                            }
                        }
                        return;
                    }

                    throw new Exception('could not save event');
                } catch (Exception $e) {
                    $transaction->rollback();
                    throw $e;
                }
            }
        } else {
            $this->setOpenElementsFromCurrentEvent('create');
            $this->updateHotlistItem($this->patient);

            if (isset(Yii::app()->session['selected_institution_id'])) {
                $worklist_manager = new \WorklistManager();

                $user_worklists = $worklist_manager->getCurrentAutomaticWorklistsForUser(null);

                $worklist_patients = array();
                foreach ($user_worklists as $user_worklist) {
                    $worklist_patients = array_merge($worklist_patients, \WorklistPatient::model()->findAllByAttributes(array('patient_id' => $this->patient->id, 'worklist_id' => $user_worklist->id)));
                }

                $applicable_pathstep = null;

                foreach ($worklist_patients as $worklist_patient) {
                    $pathway = $worklist_patient->pathway;

                    if (isset($pathway)) {
                        $pathsteps = $pathway->requested_steps;
                        foreach ($pathsteps as $pathstep) {
                            $pathstep_data = json_decode($pathstep->state_data);

                            if ($this->isEventApplicableToWorklistPathstepData($pathstep_data)) {
                                $applicable_pathstep = $pathstep;
                                break;
                            }
                        }
                    } else {
                        $pathway_type = $worklist_patient->worklist->worklist_definition->pathway_type;
                        $pathstep_types = $pathway_type->default_steps;
                        foreach ($pathstep_types as $pathstep_type) {
                            $pathstep_type_data = json_decode($pathstep_type->default_state_data);

                            if ($this->isEventApplicableToWorklistPathstepData($pathstep_type_data)) {
                                $pathway_type->instancePathway($worklist_patient);
                                $worklist_patient->refresh();

                                foreach ($worklist_patient->pathway->steps as $pathstep) {
                                    $pathstep_data = json_decode($pathstep->state_data);
                                    if ($this->isEventApplicableToWorklistPathstepData($pathstep_data)) {
                                        $applicable_pathstep = $pathstep;
                                        break;
                                    }
                                }
                                break;
                            }
                        }
                    }

                    if (isset($applicable_pathstep)) {
                        break;
                    }
                }

                if (isset($applicable_pathstep)) {
                    $applicable_pathstep->nextStatus();
                    $applicable_pathstep->refresh();

                    $pathway = $applicable_pathstep->pathway;

                    $pathway->updateStatus();

                    if ((int)$applicable_pathstep->status === PathwayStep::STEP_STARTED) {
                        Yii::app()->event->dispatch('step_started', ['step' => $applicable_pathstep]);
                    }
                }
            }
        }

        $this->editable = false;
        $this->event_tabs = array(
            array(
                'label' => 'Create',
                'active' => true,
            ),
        );

        $cancel_url = (new CoreAPI())->generatePatientLandingPageLink($this->patient);
        $this->event_actions = array(
            EventAction::link(
                'Cancel',
                Yii::app()->createUrl($cancel_url),
                array('level' => 'cancel')
            ),
        );

        $params = array(
            'errors' => $errors,
        );
        if (isset($this->eur_res) && isset($this->eur_answer_res)) {
            $params['eur_res'] = $this->eur_res;
            $params['eur_answer_res'] = $this->eur_answer_res;
        }
        $params['customErrorHeaderMessage'] = $this->customErrorHeaderMessage ?? '';
        $this->render($this->action->id, $params);
    }

    public function isEventApplicableToWorklistPathstepData($pathstep_data)
    {
        $event_type_class_name = \EventType::model()->findByPk($this->event->event_type_id)->class_name;

        return isset($pathstep_data->action_type) &&
            $pathstep_data->action_type == 'new_event' &&
            isset($pathstep_data->event_type) &&
            $pathstep_data->event_type == $event_type_class_name;
    }

    /**
     * View the event specified by $id.
     *
     * @param $id
     *
     * @throws CHttpException
     */
    public function actionView($id)
    {
        // Clean up any worklist session data that might be lingering around.
        unset(
            Yii::app()->session['active_worklist_patient_id'],
            Yii::app()->session['active_step_id'],
            Yii::app()->session['active_step_state_data']
        );
        $this->setOpenElementsFromCurrentEvent('view');
        // Decide whether to display the 'edit' button in the template
        if ($this->editable) {
            $this->editable = $this->checkEditAccess();
        }

        $this->logActivity('viewed event');

        $this->event->audit('event', 'view');

        $this->event_tabs = array(
            array(
                'label' => 'View',
                'active' => true,
            ),
        );
        if ($this->editable) {
            $this->event_tabs[] = array(
                'label' => 'Edit',
                'href' => Yii::app()->createUrl(
                    $this->event->eventType->class_name . '/default/update/' . $this->event->id
                ),
            );

            $this->event_tabs[] = array(
                'label' => 'Change Context',
                'class' => 'js-change_context'
            );
        }

        if ($this->checkDeleteAccess()) {
            $this->event_actions = array(
                EventAction::link(
                    'Delete',
                    Yii::app()->createUrl($this->event->eventType->class_name . '/default/delete/' . $this->event->id),
                    array('level' => 'delete')
                ),
            );
        } elseif ($this->checkRequestDeleteAccess()) {
            $this->event_actions = array(
                EventAction::link(
                    'Delete',
                    Yii::app()->createUrl(
                        $this->event->eventType->class_name . '/default/requestDeletion/' . $this->event->id
                    ),
                    array('level' => 'delete')
                ),
            );
        }

        $viewData = array_merge(array(
                                    'elements' => $this->open_elements,
                                    'eventId' => $id,
                                ), $this->extraViewProperties);

        $this->jsVars['OE_event_last_modified'] = strtotime($this->event->last_modified_date);

        $this->render('view', $viewData);
    }

    /**
     * The update action for the given event id.
     *
     * @param $id
     *
     * @throws CHttpException
     * @throws SystemException
     * @throws Exception
     */
    public function actionUpdate($id)
    {
        $errors = [];
        if (!empty($_POST)) {
            // somethings been submitted
            if (isset($_POST['cancel'])) {
                // Cancel button pressed, so just bounce to view
                $this->redirect(array('default/view/' . $this->event->id));
            }

            $old_template_data = $this->event->getPrefillElementsData();

            $errors = $this->setAndValidateElementsFromData($_POST);

            // update the event
            if (empty($errors)) {
                $transaction = Yii::app()->db->beginInternalTransaction();

                try {
                    //TODO: should all the auditing be moved into the saving of the event
                    $success = $this->saveEvent($_POST);

                    if ($success) {
                        if (isset(Yii::app()->session['selected_institution_id'])) {
                            $worklist_manager = new \WorklistManager();

                            $user_worklists = $worklist_manager->getCurrentAutomaticWorklistsForUser(null);

                            $worklist_patients = array();
                            foreach ($user_worklists as $user_worklist) {
                                $worklist_patients = array_merge($worklist_patients, \WorklistPatient::model()->findAllByAttributes(array('patient_id' => $this->patient->id, 'worklist_id' => $user_worklist->id)));
                            }

                            $applicable_pathstep = null;

                            foreach ($worklist_patients as $worklist_patient) {
                                $pathway = $worklist_patient->pathway;
                                $this->event->worklist_patient_id = $worklist_patient->id;

                                //If pathway hasn't been instanced, it doesn't make sense to complete a started step
                                if (isset($pathway)) {
                                    $pathsteps = $pathway->started_steps;
                                    foreach ($pathsteps as $pathstep) {
                                        $pathstep_data = json_decode($pathstep->state_data);
                                        if ($this->isEventApplicableToWorklistPathstepData($pathstep_data)) {
                                            $applicable_pathstep = $pathstep;
                                            break;
                                        }
                                    }
                                }

                                if (isset($applicable_pathstep)) {
                                    break;
                                }
                            }

                            if (isset($applicable_pathstep)) {
                                $applicable_pathstep->nextStatus();
                                $applicable_pathstep->refresh();

                                $pathway = $applicable_pathstep->pathway;

                                $pathway->updateStatus();

                                if ((int)$applicable_pathstep->status === PathwayStep::STEP_COMPLETED) {
                                    Yii::app()->event->dispatch('step_completed', ['step' => $applicable_pathstep]);
                                }
                            }
                        }

                        //TODO: should not be pasing event?
                        $this->afterUpdateElements($this->event);
                        $this->logActivity('updated event');

                        $this->event->audit('event', 'update');

                        $this->event->user = Yii::app()->user->id;

                        if (!$this->event->save()) {
                            throw new SystemException(
                                'Unable to update event: ' . print_r(
                                    $this->event->getErrors(),
                                    true
                                )
                            );
                        }

                        OELog::log("Updated event {$this->event->id}");
                        Yii::app()->event->dispatch('event_updated', ['event' => $this->event, 'action' => 'update']);
                        $transaction->commit();

                        $this->afterUpdateEvent($this->event);

                        unset(
                            Yii::app()->session['active_worklist_patient_id'],
                            Yii::app()->session['active_step_id'],
                            Yii::app()->session['active_step_state_data']
                        );

                        if ($this->event->parent_id) {
                            $this->redirect(
                                Yii::app()->createUrl(
                                    '/' . $this->event->parent->eventType->class_name . '/default/view/' . $this->event->parent_id
                                )
                            );
                        } else {
                            $template_status = $this->event->getTemplateUpdateStatusForEvent($old_template_data);

                            if ($template_status !== 'UNNEEDED') {
                                $this->redirect([$this->successUri . '?template=' . $template_status]);
                            } else {
                                $this->redirect([$this->successUri]);
                            }
                        }
                        return;
                    } else {
                        throw new Exception('Unable to save edits to event');
                    }
                } catch (Exception $e) {
                    $transaction->rollback();
                    throw $e;
                }
            }
        } else {
            // get the elements
            $this->setOpenElementsFromCurrentEvent('update');
            $this->updateHotlistItem($this->patient);

            if (isset(Yii::app()->session['selected_institution_id'])) {
                $worklist_manager = new \WorklistManager();

                $user_worklists = $worklist_manager->getCurrentAutomaticWorklistsForUser(null);

                $worklist_patients = array();
                foreach ($user_worklists as $user_worklist) {
                    $worklist_patients = array_merge($worklist_patients, \WorklistPatient::model()->findAllByAttributes(array('patient_id' => $this->patient->id, 'worklist_id' => $user_worklist->id)));
                }

                $applicable_pathstep = null;

                foreach ($worklist_patients as $worklist_patient) {
                    $pathway = $worklist_patient->pathway;

                    if (isset($pathway)) {
                        $pathsteps = $pathway->requested_steps;
                        foreach ($pathsteps as $pathstep) {
                            $pathstep_data = json_decode($pathstep->state_data);

                            if ($this->isEventApplicableToWorklistPathstepData($pathstep_data)) {
                                $applicable_pathstep = $pathstep;
                                break;
                            }
                        }
                    }

                    if (isset($applicable_pathstep)) {
                        break;
                    }
                }

                if (isset($applicable_pathstep)) {
                    $applicable_pathstep->nextStatus();
                    $applicable_pathstep->refresh();

                    $pathway = $applicable_pathstep->pathway;

                    $pathway->updateStatus();

                    if ((int)$applicable_pathstep->status === PathwayStep::STEP_STARTED) {
                        Yii::app()->event->dispatch('step_started', ['step' => $applicable_pathstep]);
                    }
                }
            }
        }

        $this->editing = true;
        $this->event_tabs = array(
            array(
                'label' => 'View',
                'href' => Yii::app()->createUrl(
                    $this->event->eventType->class_name . '/default/view/' . $this->event->id
                ),
            ),
            array(
                'label' => 'Edit',
                'active' => true,
            ),
        );

        $this->event_actions = array(
            EventAction::link(
                'Cancel',
                Yii::app()->createUrl($this->event->eventType->class_name . '/default/view/' . $this->event->id),
                array('level' => 'cancel')
            ),
        );
        $params = array(
            'errors' => $errors,
        );
        if (isset($this->eur_res) && isset($this->eur_answer_res)) {
            $params['eur_res'] = $this->eur_res;
            $params['eur_answer_res'] = $this->eur_answer_res;
        }

        $params['customErrorHeaderMessage'] = $this->customErrorHeaderMessage ?? '';
        $this->render($this->action->id, $params);
    }

    /**
     * Ajax method for loading an individual element (and its children).
     *
     * @param int $id
     * @param int $patient_id
     * @param int $previous_id
     *
     * @throws CHttpException
     * @throws Exception
     *
     * @internal param int $import_previous
     */
    public function actionElementForm($id, $patient_id, $previous_id = null, $event_id = null)
    {
        // first prevent invalid requests
        $element_type = ElementType::model()->findByPk($id);
        if (!$element_type) {
            throw new CHttpException(404, 'Unknown ElementType');
        }
        $patient = Patient::model()->findByPk($patient_id);
        if (!$patient) {
            throw new CHttpException(404, 'Unknown Patient');
        }

        // Clear script requirements as all the base css and js will already be on the page
        Yii::app()->assetManager->reset();

        $this->patient = $patient;

        if ($event_id != null) {
            $event = Event::model()->findByPk($event_id);
            $this->firm = $event->episode->firm;
        } else {
            $this->setFirmFromSession();
        }

        $this->episode = $this->getEpisode();

        // allow additional parameters to be defined by module controllers
        // TODO: Should valid additional parameters be a property of the controller?
        $additional = array();
        foreach (array_keys($_GET) as $key) {
            if (!in_array($key, array('id', 'patient_id', 'previous_id'))) {
                $additional[$key] = $_GET[$key];
            }
        }

        // retrieve the element
        $element = $this->getElementForElementForm($element_type, $previous_id, $additional);
        $this->open_elements = array($element);

        $form = Yii::app()->getWidgetFactory()->createWidget($this, 'BaseEventTypeCActiveForm', array(
            'id' => 'clinical-create',
            'enableAjaxValidation' => false,
            'htmlOptions' => array('class' => 'sliding'),
        ));

        $template_data = array();
        if ($this->template) {
            $template_detail = $this->template->getDetailRecord();
            $template_data = json_decode($template_detail->template_data, true);
        } elseif ($this->event && $this->event->template) {
            $template_detail = $this->event->template->getDetailRecord();
            $template_data = json_decode($template_detail->template_data, true);
        }

        $element_class = $element->elementType->class_name;
        $template_data_exists = !empty($template_data) && array_key_exists($element_class, $template_data);
        $element_template_data = $template_data_exists ? $template_data[$element_class] : [];

        $this->renderElement($element, 'create', $form, [], $element_template_data, array(
            'previous_parent_id' => $previous_id,
        ), false, true);
    }

    /**
     * Ajax method for viewing previous elements.
     *
     * @param int $element_type_id
     * @param int $patient_id
     *
     * @throws CHttpException
     */
    public function actionViewPreviousElements($element_type_id, $patient_id)
    {
        $element_type = ElementType::model()->findByPk($element_type_id);
        if (!$element_type) {
            throw new CHttpException(404, 'Unknown ElementType');
        }
        $this->patient = Patient::model()->findByPk($patient_id);
        if (!$this->patient) {
            throw new CHttpException(404, 'Unknown Patient');
        }

        // Clear script requirements as all the base css and js will already be on the page
        Yii::app()->assetManager->reset();

        $this->renderPartial(
            '_previous',
            array(
                'elements' => $this->getPrevious($element_type),
            ),
            false,
            true // Process output to deal with script requirements
        );
    }

    /**
     * Set the validation scenario for the element if necessary.
     *
     * @param $element
     */
    protected function setValidationScenarioForElement($element)
    {
    }

    /**
     * Determines if this is a widget based element or not, and then sets the attributes from the data accordingly
     *
     * @param $element
     * @param $data
     * @param string|int|null $index
     */
    protected function setElementAttributesFromData($element, $data, $index = null)
    {
        $model_name = \CHtml::modelName($element);
        $el_data = is_null($index) ? $data[$model_name] : $data[$model_name][$index];

        if ($widget_cls = $element->getWidgetClass()) {
            $widget = $this->createWidget($widget_cls, array(
                'patient' => $this->patient,
                'element' => $element,
                'data' => $el_data,
                'mode' => \BaseEventElementWidget::$EVENT_EDIT_MODE,
            ));
            $element->widget = $widget;
        } else {
            if (!$this->has_conflict || $element->isNewRecord) {
                $element->attributes = Helper::convertNHS2MySQL($el_data);
                $this->setElementComplexAttributesFromData($element, $data, $index);
                $element->event = $this->event;
            }
        }
        // if has conflict and the element is not a new record
        // reload element data
        if ($this->has_conflict && !$element->isNewRecord) {
            $element->refresh();
            return;
        }
    }

    /**
     * Looks for custom methods to set many to many data defined on elements. This is called prior to validation so should set values without actually
     * touching the database.
     *
     * The $data attribute will typically be the $_POST structure, but can be any appropriately structured array
     * The optional $index attribute is the counter for multiple elements of the same type that might exist in source data.
     *
     * The convention for the method name for the element setting is:
     *
     * setComplexAttributes_[element_class_name]($element, $data, $index)
     *
     * @param BaseEventTypeElement $element
     * @param array $data
     * @param int $index
     */
    protected function setElementComplexAttributesFromData($element, $data, $index = null)
    {
        $element_method = 'setComplexAttributes_' . Helper::getNSShortname($element);
        if (method_exists($this, $element_method)) {
            $this->$element_method($element, $data, $index);
        }
    }

    /**
     * Processes provided form data to create 1 or more elements of the provided type.
     *
     * @param ElementType $element_type
     * @param $data
     * @return array
     * @throws Exception
     */
    protected function getElementsForElementType(ElementType $element_type, $data)
    {
        $elements = array();
        $el_cls_name = $element_type->class_name;
        $f_key = CHtml::modelName($el_cls_name);

        $is_removed = !isset($data['element_removed'][$f_key]) || (isset($data['element_removed'][$f_key]) && !$data['element_removed'][$f_key]);

        /**
         * Check if the element has data , but not the element removed flag
         * or if the element has removed flag set and if its not set to 0
         */
        if (isset($data[$f_key]) && $is_removed) {
            $keys = array_keys($data[$f_key]);
            if (is_array($data[$f_key][$keys[0]]) && !count(array_filter(array_keys($data[$f_key]), 'is_string'))) {
                // there is more than one element of this type
                $pk_field = $el_cls_name::model()->tableSchema->primaryKey;
                foreach ($data[$f_key] as $i => $attrs) {
                    if (!$this->event->isNewRecord && !isset($attrs[$pk_field])) {
                        throw new Exception('missing primary key field for multiple elements for editing an event');
                    }
                    if ($pk = @$attrs[$pk_field]) {
                        $element = $el_cls_name::model()->findByPk($pk);
                    } else {
                        $element = $element_type->getInstance();
                    }

                    $this->setElementAttributesFromData($element, $data, $i);
                    $elements[] = $element;
                }
            } else {
                if (
                    $this->event->isNewRecord
                    || !$element = $el_cls_name::model()->find('event_id=?', array($this->event->id))
                ) {
                    $element = $element_type->getInstance();
                }
                $this->setElementAttributesFromData($element, $data);
                $elements[] = $element;
            }
        }

        return $elements;
    }

    /**
     * Unset the timestamp if the created event is an historic event
     *
     * @param $event_date
     */
    protected function setEventDate($event_date)
    {
        $event_date = Helper::convertNHS2MySQL($event_date);
        $current_event_date = substr($this->event->event_date, 0, 10);

        if ($event_date !== $current_event_date) {
            $this->event->event_date = $event_date;
        }
    }

    /**
     * Set the attributes of the given $elements from the given structured array.
     * Returns any validation errors that arise.
     *
     * @param array $data
     *
     * @return array $errors
     * @throws Exception
     *
     */
    protected function setAndValidateElementsFromData($data)
    {
        $errors = array();
        $elements = array();

        // check if the event is edited recently
        $has_last_modified_date_value = isset($data['Event']) && isset($data['Event']['last_modified_date']);
        if (
            !$this->event->isNewRecord
            && ($has_last_modified_date_value
                && $data['Event']['last_modified_date'] !== $this->event->last_modified_date)
        ) {
            $user = $this->event->usermodified->getFullName();
            $this->has_conflict = true;
            $errors['conflict'][] = "The event was recently modified by {$user} at {$this->event->last_modified_date}, please double check the entries and save again";
        }

        // only process data for elements that are part of the element type set for the controller event type
        foreach ($this->getAllElementTypes() as $element_type) {
            $from_data = $this->getElementsForElementType($element_type, $data);

            if (count($from_data) > 0) {
                $elements = array_merge($elements, $from_data);
            } elseif ($element_type->required) {
                $errors[$this->event_type->name][] = $element_type->name . ' is required';
                $elements[] = $element_type->getInstance();
            }
        }
        if (!count($elements)) {
            $errors[$this->event_type->name][] = 'Cannot create an event without at least one element';
        }

        // if has conflict
        // add missing elements
        if ($this->has_conflict) {
            $added_elements_name = array_map(function ($ele) {
                return get_class($ele);
            }, $elements);
            $event_elements = $this->event->getElements();
            foreach ($event_elements as $ele) {
                if (!in_array(get_class($ele), $added_elements_name)) {
                    $elements[] = $ele;
                }
            }
        }
        $this->open_elements = $elements;

        // validate
        foreach ($this->open_elements as $element) {
            $this->setValidationScenarioForElement($element);
            // Validate the element
            $element->validate();

            // Perform validation that requires knowledge of full event
            // scope. (ie. knowledge of which other elements are present)
            if (method_exists($element, 'eventScopeValidation')) {
                $element->eventScopeValidation($this->open_elements);
            }
            // If either validation pass has errors
            if ($element->hasErrors()) {
                $name = $element->getElementTypeName();
                foreach ($element->getErrors() as $errormsgs) {
                    foreach ($errormsgs as $error) {
                        $errors[$name][] = $error;
                    }
                }
            }
        }

        //event date and parent validation
        if (isset($data['Event']['event_date'])) {
            $this->setEventDate($data['Event']['event_date']);
            $event = $this->event;
            if (isset($data['Event']['parent_id'])) {
                $event->parent_id = $data['Event']['parent_id'];
            }
            if (!$event->validate()) {
                foreach ($event->getErrors() as $errormsgs) {
                    foreach ($errormsgs as $error) {
                        $errors[$this->event_type->name][] = $error;
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Generates the info text for controller event from the current elements, sets it on the event and saves it.
     */
    protected function updateEventInfo()
    {
        $info_text = '';
        foreach ($this->open_elements as $element) {
            if ($element->infotext) {
                $info_text .= $element->infotext;
            }
        }
        $this->event->info = $info_text;
        $this->event->save();
    }

    /**
     * Iterates through the open elements and calls the custom methods for saving complex data attributes to them.
     *
     * Custom method is of the name format saveComplexAttributes_[element_class_name]($element, $data, $index)
     *
     * @param $data
     */
    protected function saveEventComplexAttributesFromData($data)
    {
        $counter_by_cls = array();

        foreach ($this->open_elements as $element) {
            $el_cls_name = get_class($element);
            $element_method = 'saveComplexAttributes_' . Helper::getNSShortname($element);
            if (method_exists($this, $element_method)) {
                // there's custom behaviour for setting additional relations on this element class
                if (!isset($counter_by_cls[$el_cls_name])) {
                    $counter_by_cls[$el_cls_name] = 0;
                } else {
                    ++$counter_by_cls[$el_cls_name];
                }
                $this->$element_method($element, $data, $counter_by_cls[$el_cls_name]);
            }
        }
    }

    /**
     * Save the event for this controller - will create or update the event, create and update the elements, delete any
     * elements that are no longer required. Note that $data is provided for the purposes of any extensions to this
     * behaviour that might be required.
     *
     * @param $data
     *
     * @return bool
     *
     * @throws Exception
     */
    public function saveEvent($data)
    {
        if (
            isset(Yii::app()->session['active_step_id'])
            && Yii::app()->session['active_step_id'] !== $this->event->step_id
        ) {
            $step = PathwayStep::model()->findByPk(Yii::app()->session['active_step_id']);

            if ($step->getState('event_type') === $this->event->eventType->class_name) {
                $this->event->worklist_patient_id = Yii::app()->session['active_worklist_patient_id'];
                $this->event->step_id = Yii::app()->session['active_step_id'];
            }
        }

        if (!$this->event->isNewRecord) {
            // this is an edit, so need to work out what we are deleting
            $oe_ids = array();
            foreach ($this->open_elements as $o_e) {
                if ($o_e->id) {
                    if (isset($oe_ids[get_class($o_e)])) {
                        $oe_ids[get_class($o_e)][] = $o_e->id;
                    } else {
                        $oe_ids[get_class($o_e)] = array($o_e->id);
                    }
                }
            }
            // delete any elements that are no longer required for the event
            foreach ($this->event->getElements() as $curr_element) {
                if (
                    !isset($oe_ids[get_class($curr_element)])
                    || !in_array($curr_element->id, $oe_ids[get_class($curr_element)])
                ) {
                    // make sure that the element have a primary key (it tried to delete null elements before!)
                    if ($curr_element->getPrimaryKey() !== null) {
                        $curr_element->delete();
                    }
                }
            }
        } else {
            if (!$this->event->save()) {
                OELog::log(
                    "Failed to create new event for episode_id={$this->episode->id}, event_type_id=" . $this->event_type->id
                );
                throw new Exception('Unable to save event.');
            }
            if (isset($data['eur_result'])) {
                $this->saveEURForm();
            }
            OELog::log("Created new event for episode_id={$this->episode->id}, event_type_id=" . $this->event_type->id);
        }

        foreach ($this->open_elements as $element) {
            $element->event_id = $this->event->id;
            // No need to validate as it has already been validated and the event id was just generated.
            if (!$element->save(false)) {
                throw new Exception('Unable to save element ' . get_class($element) . '.');
            }
        }

        // ensure any complex data is saved to the elements
        $this->saveEventComplexAttributesFromData($data);

        // update the information attribute on the event
        $this->updateEventInfo();
        return true;
    }

    /**
     * Get the prefix name of this controller, used for path calculations for element views.
     *
     * @return string
     */
    protected function getControllerPrefix()
    {
        return strtolower(str_replace('Controller', '', Helper::getNSShortname($this)));
    }

    /**
     * Return the path alias for the module the element belongs to based on its namespace
     * (assumes elements exist in a namespace below the module namespace).
     *
     * @param BaseEventTypeElement $element
     *
     * @return string
     */
    public function getElementModulePathAlias(\BaseEventTypeElement $element)
    {
        $r = new ReflectionClass($element);

        if ($r->inNamespace()) {
            $ns_parts = explode('\\', $r->getNamespaceName());

            return implode('.', array_slice($ns_parts, 0, count($ns_parts) - 1));
        }

        return $this->modulePathAlias;
    }

    /**
     * Return the asset path for the given element (by interrogating namespace).
     *
     * @param BaseEventTypeElement $element
     *
     * @return string
     */
    public function getAssetPathForElement(\BaseEventTypeElement $element)
    {
        if ($alias = $this->getElementModulePathAlias($element)) {
            return Yii::app()->assetManager->getPublishedPathOfAlias($alias . '.assets');
        } else {
            return $this->assetPath;
        }
    }

    /**
     * calculate the alias dot notated path to an element view.
     *
     * @param BaseEventTypeElement $element
     *
     * @return string
     */
    protected function getElementViewPathAlias(\BaseEventTypeElement $element)
    {
        if ($alias = $this->getElementModulePathAlias($element)) {
            return $alias . '.views.' . $this->getControllerPrefix() . '.';
        }

        return '';
    }

    public function renderSidebar($default_view)
    {
        if (
            $this->show_element_sidebar && in_array(
                $this->getActionType($this->action->id),
                array(static::ACTION_TYPE_CREATE, static::ACTION_TYPE_EDIT),
                true
            )
        ) {
            $event_type_id = $this->event->attributes["event_type_id"];
            $event_type = EventType::model()->findByAttributes(array('id' => $event_type_id));
            $event_name = preg_replace('/\s+/', '_', $event_type->name);
            $this->renderPartial('//patient/_patient_element_sidebar', array('event_name' => $event_name));
        } else {
            parent::renderSidebar($default_view);
        }
    }

    public function renderIndexSearch()
    {
        if (
            $this->show_index_search && in_array(
                $this->getActionType($this->action->id),
                array(static::ACTION_TYPE_CREATE, static::ACTION_TYPE_EDIT),
                true
            )
        ) {
            $event_type_id = ($this->event->attributes["event_type_id"]);
            $event_type = EventType::model()->findByAttributes(array('id' => $event_type_id));
            $event_name = $event_type->name;
        }
    }

    public function renderManageElements()
    {
        if (
            $this->show_manage_elements && in_array(
                $this->getActionType($this->action->id),
                array(static::ACTION_TYPE_CREATE, static::ACTION_TYPE_EDIT),
                true
            )
        ) {
            $event_type_id = $this->event->attributes["event_type_id"];
            $event_type = EventType::model()->findByAttributes(array('id' => $event_type_id));
            $event_name = preg_replace('/\s+/', '_', $event_type->name);
            $this->renderPartial(('//patient/_patient_manage_elements'), array('event_name' => $event_name));
        }
    }


    /**
     * Extend the parent method to support inheritance of modules (and rendering the element views from the parent module).
     *
     * @param string $view
     * @param ?array $data
     * @param bool $return
     * @param bool $processOutput
     *
     * @return string
     */
    public function renderPartial($view, $data = null, $return = false, $processOutput = false)
    {
        if ($this->getViewFile($view) === false) {
            foreach ($this->getModule()->getModuleInheritanceList() as $mod) {
                // assuming that any inheritance maintains the controller name here.
                $view_path = implode('.', array($mod->id, 'views', $this->getControllerPrefix(), $view));
                if ($this->getViewFile($view_path)) {
                    $view = $view_path;
                    break;
                }
            }
        }

        return parent::renderPartial($view, $data, $return, $processOutput);
    }

    /**
     * Render the individual element based on the action provided. Note that view names
     * for the associated actions are set in the model.
     *
     * @param BaseEventTypeElement $element
     * @param string $action
     * @param BaseCActiveBaseEventTypeCActiveForm $form
     * @param array $data
     * @param array $template_data
     * @param array $view_data Data to be passed to the view.
     * @param bool $return Whether the rendering result should be returned instead of being displayed to end users.
     * @param bool $processOutput Whether the rendering result should be postprocessed using processOutput.
     *
     * @throws Exception
     */
    protected function renderElement(
        $element,
        $action,
        $form,
        $data,
        $template_data = array(),
        $view_data = array(),
        $return = false,
        $processOutput = false
    ) {
        if (is_string($action)) {
            if (strcasecmp($action, 'PDFPrint') == 0) {
                $action = 'print';
            }
            if ($action == 'savePDFprint') {
                $action = 'print';
            }

            if ($action === 'createImage' || $action === 'renderEventImage') {
                $action = 'view';
            }

            // Get the view names from the model.
            $view = isset($element->{$action . '_view'})
                ? $element->{$action . '_view'}
                : $element->getDefaultView();
            $container_view = isset($element->{'container_' . $action . '_view'})
                ? $element->{'container_' . $action . '_view'}
                : $element->getDefaultContainerView();

            $use_container_view = ($element->useContainerView && $container_view);
            $view_data = array_merge(array(
                                         'element' => $element,
                                         'data' => $data,
                                         'form' => $form,
                                         'container_view' => $container_view,
                                         'template_data' => $template_data,
                                         'prefilled' => !empty($template_data),
                                     ), $view_data);

            // Render the view.
            ($use_container_view) && $this->beginContent($container_view, $view_data);
            if ($widget_cls = $element->getWidgetClass()) {
                // only wrap the element in a widget if it's not already in one
                $widget = $element->widget ?:
                    $this->createWidget(
                        $widget_cls,
                        array(
                            'patient' => $this->patient,
                            'element' => $view_data['element'],
                            'data' => $view_data['data'],
                            'template_data' => $view_data['template_data'],
                            'prefilled' => !empty($view_data['prefilled']),
                            'mode' => $this->getElementWidgetMode($action),
                        )
                    );
                $element->widget = $widget;
                $widget->form = $view_data['form'];
                $this->renderPartial('//elements/widget_element', array('widget' => $widget), $return, $processOutput);
            } else {
                $this->renderPartial(
                    $this->getElementViewPathAlias($element) . $view,
                    $view_data,
                    $return,
                    $processOutput
                );
            }
            ($use_container_view) && $this->endContent();
        }
    }

    /**
     * Render the open elements for the controller state.
     *
     * @param string $action
     * @param BaseCActiveBaseEventTypeCActiveForm $form
     * @param array $data
     *
     * @throws Exception
     */
    public function renderOpenElements($action, $form = null, $data = null)
    {
        if ($action === 'renderEventImage') {
            $action = 'view';
        }
        $this->renderTiledElements($this->getElements($action), $action, $form, $data);
    }

    /**
     * @param $elements
     * @param $action
     * @param null $form
     * @param null $data
     * @throws CException
     * @throws Exception
     */
    public function renderTiledElements($elements, $action, $form = null, $data = null)
    {
        $element_count = count($elements);
        if ($element_count < 1) {
            return;
        }
        $rows = array(array());
        foreach ($elements as $element) {
            if ($widget_cls = $element->getWidgetClass()) {
                $widget = $element->widget ?:
                    $this->createWidget($widget_cls, array(
                        'patient' => $this->patient,
                        'element' => $element,
                        'data' => $data,
                        'mode' => $this->getElementWidgetMode($action),
                    ));

                $element->widget = $widget;
                $element->widget->renderWarnings();
            }
        }
        //Find the groupings
        $tile_index = 0;
        $row_index = 0;
        foreach ($elements as $element) {
            //if the tile size can't be determined assume a full row
            $sizeOfTile = $element->getTileSize($action) ?: $this->element_tiles_wide;
            if ($tile_index + $sizeOfTile > $this->element_tiles_wide) {
                $tile_index = 0;
                $rows[++$row_index] = array();
            }
            $rows[$row_index][] = $element;
            $tile_index += $sizeOfTile;
        }

        foreach ($rows as $row) {
            if (count($row) > 1 || ($action == 'view' && $row[0]->getTileSize($action))) {
                $this->beginWidget('TiledEventElementWidget');
                $this->renderElements($row, $action, $form, $data);
                $this->endWidget();
            } else {
                $this->renderElements($row, $action, $form, $data);
            }
        }
    }

    /**
     * @param $elements
     * @param $action
     * @param null $form
     * @param null $data
     * @throws Exception
     */
    public function renderElements($elements, $action, $form = null, $data = null)
    {
        if (count($elements) < 1) {
            return;
        }
        $template_data = array();
        if ($this->template) {
            $template_detail = $this->template->getDetailRecord();
            $template_data = json_decode($template_detail->template_data, true);
        } elseif ($this->event && $this->event->template) {
            $template_detail = $this->event->template->getDetailRecord();
            $template_data = json_decode($template_detail->template_data, true);
        }

        foreach ($elements as $element) {
            $element_class = $element->elementType->class_name;
            $template_data_exists = !empty($template_data) && array_key_exists($element_class, $template_data);
            $element_template_data = $template_data_exists ? $template_data[$element_class] : [];
            $this->renderElement(
                $element,
                $action,
                $form,
                $data,
                $element_template_data
            );
        }
    }

    /**
     * Render an optional element.
     *
     * @param BaseEventTypeElement $element
     * @param string $action
     * @param BaseCActiveBaseEventTypeCActiveForm $form
     * @param array $data
     *
     * @throws Exception
     */
    protected function renderOptionalElement($element, $action, $form, $data)
    {
        $el_view = $this->getElementViewPathAlias($element) . '_optional_' . $element->getDefaultView();
        $view = $this->getViewFile($el_view)
            ? $el_view
            : $this->getElementViewPathAlias($element) . '_optional_element';

        $this->renderPartial(
            $view,
            array(
                'element' => $element,
                'data' => $data,
                'form' => $form,
            ),
        );
    }

    /**
     * Render the optional elements for the controller state.
     *
     * @param string $action
     * @param bool $form
     * @param bool $data
     */
    public function renderOptionalElements($action, $form = null, $data = null)
    {
        foreach ($this->getOptionalElements() as $element) {
            $this->renderOptionalElement($element, $action, $form, $data);
        }
    }

    /**
     * Get all the episodes for the current patient.
     *
     * @return array
     */
    public function getEpisodes()
    {
        if (empty($this->episodes)) {
            $this->episodes = array(
                'ordered_episodes' => $this->patient->getOrderedEpisodes(),
                'legacyepisodes' => $this->patient->legacyepisodes,
                'supportserviceepisodes' => $this->patient->supportserviceepisodes,
            );
        }

        return $this->episodes;
    }

    /**
     * Get the current episode for the firm and patient.
     *
     * @return ?Episode
     */
    public function getEpisode()
    {
        return Episode::model()->getCurrentEpisodeByFirm($this->patient->id, $this->firm);
    }

    /**
     * Render the given errors with the standard template.
     *
     * @param $errors
     * @param bool $bottom
     */
    public function displayErrors($errors, $bottom = false)
    {
        $this->renderPartial('//elements/form_errors', array(
            'errors' => $errors,
            'bottom' => $bottom,
            'elements' => $this->open_elements,
        ));
    }

    /**
     * Print action.
     *
     * @param int $id event id
     */
    public function actionPrint($id)
    {
        $this->printInit($id);
        $this->printHTML($id, $this->open_elements);
    }

    /**
     * returns a suffix for PDF rendering
     */
    private function getPDFPrintSuffix()
    {
        if (method_exists($this, "getSession")) {
            $this->pdf_print_suffix .= Yii::app()->user->id . '_' . rand();
        } else {
            $this->pdf_print_suffix .= getmypid() . rand();
        }
    }

    /**
     *
     * Prepares the PDF print action by setting object variables
     *
     * @param $id
     * @param $inject_autoprint_js
     * @param $print_footer
     * @return null
     * @throws CHttpException
     * @throws Exception
     */
    public function setPDFprintData($id, $inject_autoprint_js, $print_footer = true, $module_name = null)
    {
        if (!isset($id)) {
            throw new CHttpException(400, 'No ID provided');
        }

        if (!$this->event = Event::model()->findByPk($id)) {
            throw new Exception("Event not found: " . $id);
        }

        $this->attachment_print_title = Yii::app()->request->getParam('attachment_print_title', null);

        $this->event->lock();

        $this->getPDFPrintSuffix();
        if (!$module_name) {
            $module_name = $this->module->name;
        }
        if (!$this->event->hasPDF($this->pdf_print_suffix) || @$_GET['html']) {
            // We use localhost without any port info because Puppeteer is running locally.
            $url = "http://localhost/$module_name/{$this->id}/print/{$this->event->id}{$this->print_args}";
            $this->renderAndSavePDFFromHtml($url, $inject_autoprint_js, $print_footer);
        }

        $this->event->unlock();

        return $this->pdf_print_suffix;
    }

    /**
     * Render and save a PDF file from the input HTML string
     *
     * @param $html
     * @param $inject_autoprint_js
     * @param $print_footer
     * @return null
     */
    public function renderAndSavePDFFromHtml($html, $inject_autoprint_js, $print_footer = true)
    {
        $this->getPDFPrintSuffix();

        Yii::app()->puppeteer->setDocuments($this->pdf_print_documents);
        Yii::app()->puppeteer->setDocref($this->event->docref);
        Yii::app()->puppeteer->setPatient($this->event->episode->patient);
        Yii::app()->puppeteer->setBarcode($this->event->barcodeSVG);
        Yii::app()->puppeteer->setInstitutionAndSite(
            isset($this->event->institution) ? $this->event->institution->id : null,
            isset($this->event->site) ? $this->event->site->id : null
        );

        foreach (array('left', 'middle', 'right') as $section) {
            if (isset(Yii::app()->params['puppeteer_footer_' . $section . '_' . $this->event_type->class_name])) {
                $setMethod = $section . 'FooterTemplate';
                Yii::app()->puppeteer->$setMethod = Yii::app()
                    ->params['puppeteer_footer_' . $section . '_' . $this->event_type->class_name];
            }
        }

        foreach (array('top', 'bottom', 'left', 'right') as $margin) {
            if (isset(Yii::app()->params['puppeteer_' . $margin . '_margin_' . $this->event_type->class_name])) {
                $setMethod = $margin . 'Margin';
                Yii::app()->puppeteer->$setMethod = Yii::app()
                    ->params['puppeteer_' . $margin . '_margin_' . $this->event_type->class_name];
            }
        }

        foreach (PDFFooterTag::model()->findAll('event_type_id = ?', array($this->event_type->id)) as $pdf_footer_tag) {
            if ($api = Yii::app()->moduleAPI->get($this->event_type->class_name)) {
                Yii::app()->puppeteer->setCustomTag(
                    $pdf_footer_tag->tag_name,
                    $api->{$pdf_footer_tag->method}($this->event->id)
                );
            }
        }

        Yii::app()->puppeteer->savePageToPDF(
            $this->event->imageDirectory,
            'event',
            $this->pdf_print_suffix,
            $html,
            $inject_autoprint_js,
            $print_footer,
            true,
            $this->event->id
        );

        return $this->pdf_print_suffix;
    }

    /**
     * Saves a print to PDF as a ProtectedFile object and file
     *
     * @param $id
     * @return ?array
     */
    public function actionSavePDFprint($id)
    {
        $auto_print = Yii::app()->request->getParam('auto_print', true);
        $print_footer = Yii::app()->request->getParam('print_footer', 'true') === 'true';
        $inject_autoprint_js = $auto_print == "0" ? false : $auto_print;

        $pdf_route = $this->setPDFprintData($id, $inject_autoprint_js, $print_footer);
        $pf = ProtectedFile::createFromFile($this->event->imageDirectory . '/event_' . $pdf_route . '.pdf');
        if ($pf->save()) {
            $result = array(
                'success' => 1,
                'file_id' => $pf->id,
            );

            if (!isset($_GET['ajax'])) {
                $result['name'] = $pf->name;
                $result['mime'] = $pf->mimetype;
                $result['path'] = $pf->getPath();

                return $result;
            }
        } else {
            $result = array(
                'success' => 0,
                'message' => "couldn't save file object" . print_r($pf->getErrors(), true),
            );
        }

        $this->renderJSON($result);
    }

    /**
     * @param $id
     * @return mixed|void
     */
    public function actionPDFPrint($id)
    {
        $auto_print = Yii::app()->request->getParam('auto_print', true);
        $print_footer = Yii::app()->request->getParam('print_footer', 'true') === 'true';

        $inject_autoprint_js = $auto_print == "0" ? false : $auto_print;

        $pdf_route = $this->setPDFprintData($id, $inject_autoprint_js, $print_footer);
        if (@$_GET['html']) {
            return Yii::app()->end();
        }

        $pdf = $this->event->getPDF($pdf_route);


        header('Content-Type: application/pdf');
        header('Content-Length: ' . filesize($pdf));

        readfile($pdf);
        @unlink($pdf);
    }

    /**
     * Initialise print action.
     *
     * @param int $id event id
     *
     * @throws CHttpException
     * @TODO: standardise printInit function as per init naming convention
     */
    protected function printInit($id)
    {
        if (!$this->event = Event::model()->findByPk($id)) {
            throw new CHttpException(403, 'Invalid event id.');
        }
        $this->patient = $this->event->episode->patient;
        $this->episode = $this->event->episode;
        $this->site = Site::model()->findByPk(Yii::app()->session['selected_site_id']);
        $this->setOpenElementsFromCurrentEvent('print');
    }

    /**
     * Render HTML print layout.
     *
     * @param int $id event id
     * @param BaseEventTypeElement[] $elements
     * @param string $template
     */
    protected function printHTML($id, $elements, $template = 'print')
    {
        $this->layout = '//layouts/print';
        $this->render($template, array(
            'elements' => $elements,
            'eventId' => $id,
        ));
    }

    public function printHTMLCopy($id, $elements, $template = 'print')
    {
        $this->layout = '//layouts/printCopy';
        $result = $this->render(
            $template,
            array(
                'elements' => $elements,
                'eventId' => $id,
            ),
            true
        );

        echo $result;
    }

    /**
     * Log print action.
     *
     * @param int $id event id
     * @param bool $pdf
     */
    protected function printLog($id, $pdf)
    {
        $this->logActivity("printed event (pdf=$pdf)");
        $this->event->audit('event', (strpos($this->pdf_print_suffix, 'all') === 0 ? 'print all' : 'print'), false);
    }

    /**
     * Run this function after soft delete happened
     *
     * @param $event
     * @return bool
     */
    public function afterSoftDelete($event)
    {
        return true;
    }

    /**
     * Delete the event given by $id. Performs the soft delete action if it's been confirmed by $_POST.
     *
     * @param $id
     *
     * @throws CHttpException
     * @throws Exception
     */
    public function actionDelete($id)
    {
        if (isset($_POST['et_canceldelete'])) {
            return $this->redirect(array('/' . $this->event_type->class_name . '/default/view/' . $id));
        }

        if (!empty($_POST)) {
            if (Yii::app()->request->getPost('delete_reason', '') === '') {
                $errors = array('Reason for deletion' => array('Please enter a reason for deleting this event'));
            } else {
                $transaction = Yii::app()->db->beginTransaction();
                try {
                    $this->event->softDelete(Yii::app()->request->getPost('delete_reason', ''));

                    $this->event->audit('event', 'delete', false);

                    if (Event::model()->count('episode_id=?', array($this->event->episode_id)) == 0) {
                        $this->event->episode->deleted = 1;
                        if (!$this->event->episode->save()) {
                            throw new Exception(
                                'Unable to save episode: ' . print_r(
                                    $this->event->episode->getErrors(),
                                    true
                                )
                            );
                        }

                        $this->event->episode->audit('episode', 'delete', false);

                        $transaction->commit();

                        if (!$this->dont_redirect) {
                            $this->redirect(
                                (new CoreAPI())->generatePatientLandingPageLink($this->event->episode->patient)
                            );
                        } else {
                            return true;
                        }
                    }

                    Yii::app()->user->setFlash(
                        'success',
                        'An event was deleted, please ensure the episode status is still correct.'
                    );
                    $transaction->commit();

                    $this->afterDeleteEvent($this->event);

                    if (!$this->dont_redirect) {
                        $this->redirect(
                            (new CoreAPI())->generatePatientLandingPageLink($this->event->episode->patient)
                        );
                    }

                    return true;
                } catch (Exception $e) {
                    $transaction->rollback();
                    throw $e;
                }
            }
        }

        $this->title = 'Delete ' . $this->event_type->name;

        $this->event_tabs = array(
            array(
                'label' => 'View',
                'active' => true,
            ),
        );
        if ($this->editable) {
            $this->event_tabs[] = array(
                'label' => 'Edit',
                'href' => Yii::app()->createUrl(
                    $this->event->eventType->class_name . '/default/update/' . $this->event->id
                ),
            );
        }

        $this->processJsVars();

        $episodes = $this->getEpisodes();
        $viewData = array_merge(array(
                                    'eventId' => $id,
                                    'errors' => isset($errors) ? $errors : null,
                                ), $episodes);

        $this->render('delete', $viewData);
    }

    /**
     * Called after the event is saved, transaction is commited
     * useful for creating additional events automatically with their own transactions
     *
     * @param \Event $event
     */
    protected function afterCreateEvent($event)
    {
        $this->updateFollowUpAggregate();
    }

    /**
     * Called after the event is saved, transaction is commited
     * useful for PAS callouts following successful saving of an updated event
     *
     * @param \Event $event
     */
    protected function afterUpdateEvent($event)
    {
        $this->updateFollowUpAggregate();
    }

    /**
     * Called after the event is deleted, transaction is commited
     * useful for PAS callouts following successful deletion of an event
     *
     * @param \Event $event
     */
    protected function afterDeleteEvent($event)
    {
        $this->updateFollowUpAggregate();
    }

    /**
     * Called after event (and elements) has been updated.
     *
     * @param Event $event
     */
    protected function afterUpdateElements($event)
    {
        $this->updateUniqueCode($event);
    }

    /**
     * Called after event (and elements) have been created.
     *
     * @param Event $event
     */
    protected function afterCreateElements($event)
    {
        $this->updateUniqueCode($event);

        $site_id = \Yii::app()->session->get('selected_site_id');
        $firm_id = \Yii::app()->session->get('selected_firm_id');
        if (!$event->worklist_patient_id) {
            $this->addToUnbookedWorklist($site_id, $firm_id);
        }
    }

    /**
     * Update Unique code for the event associated the specific procedures.
     */
    private function updateUniqueCode($event)
    {
        foreach ($this->unique_code_elements as $unique) {
            if ($event->eventType->class_name === $unique['event']) {
                foreach ($event->getElements() as $element) {
                    if (in_array(Helper::getNSShortname($element), $unique['element'])) {
                        $event_unique_code = UniqueCodeMapping::model()->findAllByAttributes(
                            array('event_id' => $event->id)
                        );
                        if (!$event_unique_code) {
                            $this->createNewUniqueCodeMapping($event->id, null);
                        }
                    }
                }
            }
        }
    }

    /**
     * set base js vars for use in the standard scripts for the controller.
     */
    public function processJsVars()
    {
        if ($this->patient) {
            $patient_identifier = PatientIdentifier::model()->find(
                'patient_id=:patient_id AND patient_identifier_type_id=:patient_identifier_type_id',
                [
                    ':patient_id' => $this->patient->id,
                    ':patient_identifier_type_id' => SettingMetadata::model()->getSetting('oelauncher_patient_identifier_type')
                ]
            );
            $this->jsVars['OE_patient_id'] = $this->patient->id;
            $this->jsVars['OE_patient_hosnum'] = $patient_identifier->value ?? null;
        }
        if ($this->event) {
            $this->jsVars['OE_event_id'] = $this->event->id;

            if (SettingMetadata::model()->getSetting('event_print_method') == 'pdf') {
                $this->jsVars['OE_print_url'] = Yii::app()->createUrl(
                    $this->getModule()->name . '/default/PDFprint/' . $this->event->id
                );
            } else {
                $this->jsVars['OE_print_url'] = Yii::app()->createUrl(
                    $this->getModule()->name . '/default/print/' . $this->event->id
                );
            }
        }
        if ($this->episode) {
            $this->jsVars['OE_episode_id'] = $this->episode->id;
        }

        $this->jsVars['OE_asset_path'] = $this->assetPath;
        $firm = Firm::model()->findByPk(Yii::app()->session->get('selected_firm_id'));
        $subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;
        $this->jsVars['OE_subspecialty_id'] = $subspecialty_id;
        $this->jsVars['OE_site_id'] = Yii::app()->session['selected_site_id'];

        parent::processJsVars();
    }

    /**
     * Sets the the css state.
     */
    protected function initActionRequestDeletion()
    {
        $this->moduleStateCssClass = 'view';

        $this->initWithEventId(@$_GET['id']);
    }

    /**
     * Action to process delete requests for an event.
     *
     * @param $id
     *
     * @return bool|void
     *
     * @throws CHttpException
     */
    public function actionRequestDeletion($id)
    {
        if (!$this->event = Event::model()->findByPk($id)) {
            throw new CHttpException(403, 'Invalid event id.');
        }

        if (isset($_POST['et_canceldelete'])) {
            return $this->redirect(array('/' . $this->event->eventType->class_name . '/default/view/' . $id));
        }

        $this->patient = $this->event->episode->patient;

        $errors = array();

        if (!empty($_POST)) {
            if (!@$_POST['delete_reason']) {
                $errors = array('Reason' => array('Please enter a reason for deleting this event'));
            } else {
                $this->event->requestDeletion($_POST['delete_reason']);

                if (SettingMetadata::model()->getSetting('admin_email')) {
                    mail(
                        SettingMetadata::model()->getSetting('admin_email'),
                        'Request to delete an event',
                        'A request to delete an event has been submitted.  Please log in to the admin system to review the request.',
                        'From: OpenEyes'
                    );
                }

                Yii::app()->user->setFlash('success', 'Your request to delete this event has been submitted.');

                header(
                    'Location: ' . Yii::app()->createUrl(
                        '/' . $this->event_type->class_name . '/default/view/' . $this->event->id
                    )
                );

                return true;
            }
        }

        $this->title = 'Delete ' . $this->event_type->name;
        $this->event_tabs = array(
            array(
                'label' => 'View',
                'active' => true,
            ),
        );

        $this->render('delete', array(
            'errors' => $errors,
        ));
    }

    /**
     * Get open element by class name.
     *
     * @param string $class_name
     *
     * @return object
     */
    public function getOpenElementByClassName($class_name)
    {
        if (!empty($this->open_elements)) {
            foreach ($this->open_elements as $element) {
                if (CHtml::modelName($element) == $class_name) {
                    return $element;
                }
            }
        }

        return;
    }

    /**
     * Set the open elements (for unit testing).
     *
     * @param array $open_elements
     */
    public function setOpenElements($open_elements)
    {
        $this->open_elements = $open_elements;
    }

    public function readInEventImageSettings()
    {
        $this->event = Event::model()->findByPk($_GET['id']);
        if (!isset($this->event) || !isset($this->event->eventType)) {
            return;
        }

        $event_params = array();
        if (array_key_exists('event_specific', Yii::app()->params['lightning_viewer'])) {
            $lightning_params = Yii::app()->params['lightning_viewer']['event_specific'];
            if (array_key_exists($this->event->eventType->name, $lightning_params)) {
                $event_params = $lightning_params[$this->event->eventType->name];
            }
        }

        if (!isset($event_params)) {
            return;
        };

        foreach ($event_params as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function actionEventImage()
    {
        if (!$event = Event::model()->findByPk(@$_GET['event_id'])) {
            throw new Exception('Event not found: ' . @$_GET['event_id']);
        }

        if (!$event->hasEventImage(@$_GET['image_name'])) {
            throw new Exception("Event $event->id image missing: " . @$_GET['image_name']);
        }

        $path = $event->getImagePath(@$_GET['image_name']);

        header('Content-Type: image/jpeg');
        header('Content-Length: ' . filesize($path));
        readfile($path);
    }

    /**
     * @throws CException
     */
    protected function persistPcrRisk()
    {
        $pcrRisk = new \PcrRisk();
        $pcrData = Yii::app()->request->getPost('PcrRisk', array());
        foreach ($pcrData as $side => $sideData) {
            $pcrRisk->persist($side, $this->patient, $sideData);
        }
    }


    /**
     * Gets the extra info to be displayed in the title of this event
     * returns null if no extra info exists
     *
     * @return string|null HTML to display next to the title
     */
    public function getExtraTitleInfo()
    {
        return null;
    }

    protected function updateHotlistItem(Patient $patient)
    {
        $user = Yii::app()->user;
        $hotlistItem = UserHotlistItem::model()->find(
            'created_user_id = :user_id AND patient_id = :patient_id
                       AND (DATE(last_modified_date) = :current_date OR is_open = 1)',
            array(':user_id' => $user->id, ':patient_id' => $patient->id, ':current_date' => date('Y-m-d'))
        );

        if (!$hotlistItem) {
            $hotlistItem = new UserHotlistItem();
            $hotlistItem->patient_id = $patient->id;
        }

        $hotlistItem->is_open = 1;
        if (!$hotlistItem->save()) {
            throw new Exception('UserHotListItem failed validation ' . print_r($hotlistItem->errors, true));
        };
    }


    /**
     * Creates the preview image for the event with the given ID
     *
     * @param integer $id The ID of the event to image
     * @throws Exception
     */
    public function actionCreateImage($id)
    {
        $this->initActionView();
        // Stub an EventImage record so other threads don't try to create the same image
        $eventImage = $this->saveEventImage('GENERATING');

        $this->readInEventImageSettings();
        try {
            Yii::app()->params['image_generation'] = true;      // Change the theme to dark for lightning viewer image
            $content = $this->getEventAsHtml();
            Yii::app()->params['image_generation'] = false;     // Chane the theme back to normal

            $image = Yii::app()->puppeteer;
            $image->savePageToImage(
                $this->event->getImageDirectory(),
                'preview',
                '',
                $content,
                [
                    'width' => Yii::app()->params['lightning_viewer']['image_width'],
                    'viewport_width' => Yii::app()->params['lightning_viewer']['viewport_width']
                ]
            );

            $input_path = $this->event->getImagePath('preview');
            $output_path = $this->event->getImagePath('preview', '.jpg');
            $imagick = new Imagick($input_path);
            $imagick->writeImage($output_path);

            $this->saveEventImage('CREATED', ['image_path' => $output_path]);

            if (!Yii::app()->params['lightning_viewer']['keep_temp_files']) {
                $image->deleteFile($input_path);
                $image->deleteFile($output_path);
            }

            $document_number = 0;
            foreach ($this->event->eventAttachmentGroups as $attachmentGroup) {
                foreach ($attachmentGroup->eventAttachmentItems as $attachmentItem) {
                    if (!$attachmentItem) {
                        continue;
                    }

                    switch ($attachmentItem->attachmentData->mime_type) {
                        case 'application/pdf':
                        case 'image/jpeg':
                        case 'image/png':
                            $this->createPdfPreviewImages(
                                null,
                                $attachmentItem->attachmentData->bodySiteSnomedType->getEye(),
                                $attachmentItem->attachmentData,
                                $document_number
                            );
                            break;
                        default:
                            break;
                    }
                    $document_number++;
                }
            }
        } catch (Exception $ex) {
            // Store an error entry,so that no attempts are made to generate the image again until the errors are fixed
            $this->saveEventImage('FAILED', ['message' => (string)$ex]);
            throw $ex;
        }
    }

    /**
     * Scales down the input image if it is larger than the maximum width
     *
     * @param Imagick $imagick
     */
    protected function scaleImageForThumbnail($imagick)
    {
        $width = $this->image_width ?: 800;
        if ($width < $imagick->getImageWidth()) {
            $height = $width * $imagick->getImageHeight() / $imagick->getImageWidth();
            $imagick->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 1);
            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @throws CHttpException
     */
    public function actionRenderEventImage($id)
    {
        if (!$this->event) {
            $this->event = Event::model()->findByPk($id);
        }
        if (!$this->patient) {
            $this->setPatient($this->event->episode->patient_id);
        }
        if (!$this->episode) {
            $this->episode = $this->event->episode;
        }

        $this->setOpenElementsFromCurrentEvent('view');

        $viewData = array_merge(array(
                                    'elements' => $this->open_elements,
                                    'eventId' => $this->event->id,
                                ), $this->extraViewProperties);

        $this->moduleStateCssClass = 'view';

        $this->layout = '//layouts/event_image';
        $this->render('image', $viewData);
    }

    /**
     * Renders the event and returns the resullting HTML
     *
     * @return string The output HTML
     */
    protected function getEventAsHtml()
    {
        $content = Yii::app()->createAbsoluteUrl(
            "{$this->getModule()->name}/{$this->id}/renderEventImage/{$this->event->id}"
        );

        return $content;
    }

    /**
     * Gets the image path that will be used to store a temporary preview image
     *
     * @param array $options Additional options, including the page number, and eye
     * @param string $extension The file extension of the path (defaults to '.png')
     * @return string The path of the image
     */
    public function getPreviewImagePath(array $options = array(), $extension = '.png')
    {
        $filename = 'preview';

        if (isset($options['eye'])) {
            $filename .= '-' . $options['eye'];
        }

        if (isset($options['document_number'])) {
            $filename .= '-' . $options['document_number'];
        }

        if (isset($options['page'])) {
            $filename .= '-' . $options['page'];
        }

        $path = $this->event->getImagePath($filename, $extension);

        if (!file_exists(dirname($path))) {
            if (!is_dir(dirname($path)) && !file_exists(dirname($path))) {
                mkdir(dirname($path), 0774, true);
            }
        }

        return $path;
    }

    /**
     * Removes all preview images for this event
     */
    protected function removeEventImages()
    {
        EventImage::model()->deleteAll('event_id = :event_id', ['event_id' => $this->event->id]);
    }

    /**
     * Saves a new EventImage record with the given status, and other options
     * Without additional options, only a stub will be created
     *
     * @param 0string $status The name of the status to use. Can be one of 'GENERATING', 'NOT_CREATED', 'FAILED' or 'COMPLETE'
     * @param array $options Additional options, including the page, eye_id, image_path, and error message
     * @return EventImage The created EventImage record
     * @throws Exception
     */
    protected function saveEventImage($status, array $options = [])
    {
        $criteria = new CDbCriteria();
        $criteria->compare('event_id', $this->event->id);
        if (isset($options['page'])) {
            $criteria->addCondition('(page IS NULL OR page = :page)');
            $criteria->params[':page'] = $options['page'];
        }

        if (isset($options['eye_id'])) {
            $criteria->addCondition('(eye_id IS NULL OR eye_id = :eye_id)');
            $criteria->params[':eye_id'] = $options['eye_id'];
        }

        if (isset($options['document_number'])) {
            $criteria->addCondition('(document_number = :document_number)');
            $criteria->params[':document_number'] = $options['document_number'];
        } else {
            $criteria->addCondition('(document_number IS NULL)');
        }

        $eventImage = EventImage::model()->find($criteria) ?: new EventImage();
        $eventImage->event_id = $this->event->id;
        if (isset($options['image_path'])) {
            $eventImage->image_data = file_get_contents($options['image_path']);

            if (!Yii::app()->params['lightning_viewer']['keep_temp_files']) {
                @unlink($options['image_path']);
            }
        }

        $eventImage->eye_id = @$options['eye_id'];
        $eventImage->page = @$options['page'];
        $eventImage->document_number = isset($options['document_number']) ? $options['document_number'] : null;
        $eventImage->attachment_data_id = isset($options['attachment_data_id']) ? $options['attachment_data_id'] : null;
        $eventImage->status_id = EventImageStatus::model()->find('name = ?', array($status))->id;

        if (isset($options['message'])) {
            $eventImage->message = $options['message'];
        }

        if (!$eventImage->save()) {
            throw new Exception('Could not save event image: ' . print_r($eventImage->getErrors(), true));
        }

        return $eventImage;
    }

    /**
     * Creates preview images for all pages of the given PDF file
     *
     * @param string $pdf_path The path for the PDF file
     * @param int|null $eye The eye ID the PDF is for
     * @throws Exception
     */
    protected function createPdfPreviewImages($pdf_path, $eye = null, $attachment_data = null, $document_number = null)
    {
        $attachment_data_id = null;
        if ($attachment_data !== null) {
            $attachment_data_id = $attachment_data->id;
        }
        $pdf_imagick = new Imagick();
        if ($attachment_data == null) {
            $pdf_imagick->readImage($pdf_path);
        } else {
            $pdf_imagick->readImageBlob($attachment_data->blob_data);
        }
        $pdf_imagick->setImageFormat('jpeg');
        $original_width = $pdf_imagick->getImageGeometry()['width'];
        if ($this->image_width != 0 && $original_width != $this->image_width) {
            $original_res = $pdf_imagick->getImageResolution()['x'];
            $new_res = $original_res * ($this->image_width / $original_width);

            $pdf_imagick = new Imagick();
            $pdf_imagick->setResolution($new_res, $new_res);
            if ($attachment_data == null) {
                $pdf_imagick->readImage($pdf_path);
            } else {
                $pdf_imagick->readImageBlob($attachment_data->blob_data);
            }
            $pdf_imagick->setImageCompressionQuality($this->compression_quality);
        }

        $output_path = $this->getPreviewImagePath(['eye' => $eye, 'document_number' => $document_number]);
        if (!$pdf_imagick->writeImages($output_path, false)) {
            throw new Exception('An error occurred when attempting to convert eh PDF file to images');
        }

        // Try to save the PDF as though it only has one page
        $result = $this->savePdfPreviewAsEventImage(null, $eye, $document_number, $attachment_data_id);
        if (!$result) {
            // If nothing was saved, then it has multiple pages
            for ($page = 0;; ++$page) {
                $result = $this->savePdfPreviewAsEventImage($page, $eye, $document_number, $attachment_data_id);
                if (!$result) {
                    break;
                }
            }
        }
    }

    /**
     * Attempts to create the EventImage record for the given page
     *
     * @param int|null $page The page number of the PDF
     * @param int|null $eye The eye side if it exists
     * @return bool True if the page exists, otherwise false
     * @throws ImagickException Thrown if the layers can't be merged
     * @throws Exception
     */
    protected function savePdfPreviewAsEventImage($page, $eye, $document_number, $attachment_data_id)
    {
        $pagePreviewPath = $this->getPreviewImagePath(
            ['page' => $page, 'eye' => $eye, 'document_number' => $document_number]
        );
        if (!file_exists($pagePreviewPath)) {
            return false;
        }

        $imagickPage = new Imagick();
        $imagickPage->readImage($pagePreviewPath);

        // Sometimes the PDf has a transparent background, which should be replaced with white
        $this->whiteOutImageImagickBackground($imagickPage);

        // in case some other process removed the file or directory
        $pagePreviewPath = $this->getPreviewImagePath(
            ['page' => $page, 'eye' => $eye, 'document_number' => $document_number]
        );
        $imagickPage->writeImage($pagePreviewPath);
        $this->saveEventImage(
            'CREATED',
            [
                'image_path' => $pagePreviewPath,
                'page' => $page,
                'eye_id' => $eye,
                'document_number' => $document_number,
                'attachment_data_id' => $attachment_data_id
            ]
        );

        if (!Yii::app()->params['lightning_viewer']['keep_temp_files']) {
            @unlink($pagePreviewPath);
        }

        return true;
    }

    /**
     * Makes transparent imagick images have a white background
     *
     * @param $imagick Imagick
     * @throws Exception
     */
    protected function whiteOutImageImagickBackground($imagick)
    {
        if ($imagick->getImageAlphaChannel()) {
            // 11 Is the alphachannel_flatten value , a hack until all machines use the same imagick version
            $imagick->setImageAlphaChannel(
                defined('Imagick::ALPHACHANNEL_FLATTEN') ? Imagick::ALPHACHANNEL_FLATTEN : 11
            );
            $imagick->setImageBackgroundColor('white');
            $imagick->mergeImageLayers(imagick::LAYERMETHOD_FLATTEN);
        }
    }


    /**
     * Searches for tags used by Eyedraw via AJAX
     */
    public function actionEDTagSearch()
    {
        $term = $_GET["EDSearchTerm"];

        $result_models = EyedrawTag::model()->findAll("text LIKE '%" . strtolower($term) . "%'");

        $processed_results =
            array_map(
                function ($result) {
                    return ['pk_id' => $result->id, 'text' => $result->text, 'snomed_code' => $result->snomed_code];
                },
                $result_models
            );

        echo json_encode($processed_results);
    }

    /**
     * @param Firm $context
     * @throws CHttpException
     */
    protected function setContext(Firm $context)
    {
        // get the user
        $user_id = $this->getApp()->user->id;
        $user = User::model()->findByPk($user_id);

        // set the firm on the user (process taken from SiteAndFirmWidget)
        $user->changeFirm($context->id);
        if (!$user->save(false)) {
            throw new CHttpException(404, 'Unexpected error setting user context.');
        }

        $this->selectedFirmId = $context->id;

        $user->audit('user', 'change-firm', $user->last_firm_id);
        $this->getApp()->session['selected_firm_id'] = $context->id;
    }

    /**
     * prevent the user accessing non-deleted event with the view for removed
     * if the user has no permission or the setting is off, the view for revmoed event is not accessible
     * @throws CHttpException
     */
    protected function initActionRemoved()
    {
        $this->initWithEventId(@$_GET['id']);
        if (!$this->event->deleted) {
            $this->redirect($this->event->getEventViewPath());
        }
    }

    /**
     * Find and then associate an appropriate step to the supplied event before updating that step
     * @param $event - The event to use; if not supplied, defaults to $this->event from the controller
     */
    protected function updateEventStep($event = null)
    {
        $event ??= $this->event;

        if (isset(Yii::app()->session['selected_institution_id'])) {
            $worklist_manager = new \WorklistManager();

            $user_worklists = $worklist_manager->getCurrentAutomaticWorklistsForUser(null);

            $worklist_patients = array();
            foreach ($user_worklists as $user_worklist) {
                $worklist_patients = array_merge($worklist_patients, \WorklistPatient::model()->findAllByAttributes(array('patient_id' => $this->patient->id, 'worklist_id' => $user_worklist->id)));
            }

            $applicable_pathstep = null;

            foreach ($worklist_patients as $worklist_patient) {
                $pathway = $worklist_patient->pathway;
                $event->worklist_patient_id = $worklist_patient->id;

                //If pathway hasn't been instanced, it doesn't make sense to complete a started step
                if (isset($pathway)) {
                    $pathsteps = $pathway->started_steps;
                    foreach ($pathsteps as $pathstep) {
                        $pathstep_data = json_decode($pathstep->state_data);
                        if ($this->isEventApplicableToWorklistPathstepData($pathstep_data)) {
                            $applicable_pathstep = $pathstep;
                            break;
                        }
                    }
                }

                if (isset($applicable_pathstep)) {
                    // bind the step id to the event, when the event_created event is dispatched, only relevant step will be marked as completed
                    $event->step_id = $applicable_pathstep->id;
                    break;
                }
            }

            if (isset($applicable_pathstep)) {
                $applicable_pathstep->nextStatus();
                $applicable_pathstep->refresh();

                $pathway = $applicable_pathstep->pathway;

                $pathway->updateStatus();

                if ((int)$applicable_pathstep->status === PathwayStep::STEP_COMPLETED) {
                    Yii::app()->event->dispatch('step_completed', ['step' => $applicable_pathstep]);
                }

                if (isset($pathway->requested_steps[0])) {
                    $next_pathstep = $pathway->requested_steps[0];

                    if ($next_pathstep->type->type == "hold") {
                        $next_pathstep->nextStatus();
                        $next_pathstep->refresh();

                        $pathway->updateStatus();

                        if ((int)$next_pathstep->status === PathwayStep::STEP_STARTED) {
                            Yii::app()->event->dispatch('step_started', ['step' => $next_pathstep]);
                        }
                    }
                }
            }
        }
    }

    /**
     * View the removed event specified by $id.
     * @param id event_id
     *
     * @throws CHttpException
     */
    public function actionRemoved($id)
    {
        $this->setOpenElementsFromCurrentEvent('view');
        $this->editable = false;
        $this->event->audit('event', 'view removed');
        $this->event_tabs = array(
            array(
                'label' => 'Deleted Event - do not use',
                'class' => 'highlighter warning',
                'active' => true,
                'type' => 'span',
            ),
        );

        $criteria = new CDbCriteria();
        $criteria->compare('delete_pending', 1);
        $event_previous_version = $this->event->getPreviousVersionWithCriteria($criteria);

        $viewData = array_merge(array(
            'elements' => $this->open_elements,
            'eventId' => $id,
            'event_previous_version' => $event_previous_version
        ), $this->extraViewProperties);
        $this->jsVars['OE_event_last_modified'] = strtotime($this->event->last_modified_date);
        $this->render('//deleted_events/removed', $viewData);
    }

    public function actionSaveTemplate()
    {
        $template_name = \Yii::app()->request->getParam('template_name');
        $event_id = \Yii::app()->request->getParam('event_id');
        $event = Event::model()->findByPk($event_id);

        if ($event) {
            $template = $event->createTemplateFromEvent($template_name);

            if ($template) {
                $this->redirect(array($this->successUri . $event->id));
            } else {
                throw new Exception("Could not create template: unable to save data");
            }
        } else {
            throw new Exception("Could not create template: event not found");
        }
    }

    public function actionUpdateTemplate()
    {
        $template_id = \Yii::app()->request->getParam('template_id');
        $event_id = \Yii::app()->request->getParam('event_id');

        $event = Event::model()->findByPk($event_id);

        if ($event) {
            $event->updateTemplateFromEvent($template_id);

            $this->redirect(array($this->successUri . $event->id));
        } else {
            throw new Exception("Could not update template: event not found");
        }
    }

    private function updateFollowUpAggregate()
    {
        if (!empty($this->patient)) {
            \FollowupAnalysisAggregate::updateForPatientExaminationOrDocument($this->patient->id);
        }
    }
}
