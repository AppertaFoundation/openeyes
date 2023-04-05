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
 * A class that all clinical elements should extend from.
 *
 * @property bool $useContainerView When rendering the element, wrap the element
 * in a container view?
 * @property Event $event
 * @property string|int $event_id
 * @property EventType $eventType
 * @property ElementType $elementType
 */
class BaseEventTypeElement extends BaseElement
{
    public $firm;
    public $userId;
    public $patientId;
    public $useContainerView = true;
    // allow us to store a widget on the element so that it doesn't have to widgetised twice
    public $widget = null;
    public $is_initialized = false;
    /**
     * set to true for the element to load from previous
     * @see BaseElement::loadFromExisting
     */
    protected $default_from_previous = false;

    // array of audit messages
    protected $audit = array();

    /**
     * @var BaseEventElementWidget Defines the widget class to manage the element
     * @see getWidgetClass
     */
    protected $widgetClass = null;

    protected $_element_type;
    protected $frontEndErrors = array();
    protected $errorExceptions = array(
    );

    private $settings = array();

    /**
     * Return the title of the element to be displayed in view mode
     * Should be overridden by subclasses to customise the title
     *
     * @return string The title to be displayed
     */
    public function getViewTitle()
    {
        return $this->getElementTypeName();
    }

    /**
     * Return the element type name.
     *
     * @return string $name
     */
    public function getElementTypeName()
    {
        return $this->getElementType()->name;
    }

    /**
     * Get the ElementType for this element.
     *
     * @return ElementType
     */
    public function getElementType()
    {
        if (!$this->_element_type) {
            $this->_element_type = ElementType::model()->find('class_name=?', array(get_class($this)));
        }

        return $this->_element_type;
    }

    /**
     * Return the title of the element to be displayed in edit mode
     * Should be overridden by subclasses to customise the title
     *
     * @return string The title to be displayed
     */
    public function getFormTitle()
    {
        return $this->getElementTypeName();
    }

    /**
     * Can this element be copied (cloned/duplicated)
     * Override to return true if you want an element to be copyable.
     *
     * @return bool
     */
    public function canCopy()
    {
        return false;
    }

    /**
     * Can we view the previous version of this element.
     *
     * @return bool
     */
    public function canViewPrevious()
    {
        return false;
    }

    public function getDisplayAttributes()
    {
        return $this->getAttributes();
    }

    /**
     * Return the width of this element in tiles for viewing
     * @return null
     */
    public function getTileSize($action)
    {
        return null;
    }

    /**
     * Is this element required in the UI? (Prevents the user from being able
     * to remove the element.).
     *
     * @return bool
     */
    public function isRequiredInUI()
    {
        return $this->isRequired();
    }

    /**
     * Is this a required element?
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->elementType->required;
    }

    /**
     * Is this element to be hidden in the UI? (Prevents the elements from
     * being displayed on page load.).
     *
     * @return bool
     */
    public function isHiddenInUI()
    {
        return false;
    }

    public function render($action)
    {
        $this->Controller->renderPartial();
    }

    public function getSetting($key)
    {
        $setting = SettingMetadata::model()->getSetting($key, ElementType::model()->find('class_name=?', array(get_class($this))));
        if (!$setting) {
            $setting = SettingMetadata::model()->getSetting($key, ElementType::model()->find('class_name=?', array(get_parent_class($this))));
        }

        return $setting;
    }

    /**
     * @param \Patient $patient
     */
    public function setDefaultOptions(\Patient $patient = null)
    {
        if ($this->default_from_previous && $patient) {
            if ($previous = $this->getMostRecentForPatient($patient)) {
                $this->loadFromExisting($previous);
            }
        }
    }

    public function getDefaultFormOptions(array $context): array
    {
        $fields = array();
        if ($this->default_from_previous && $context['patient']) {
            if ($previous = $this->getMostRecentForPatient($context['patient'])) {
                $fields = $this->getFromExisting($previous);
            }
        }
        return $fields;
    }

    /**
     * Get the most recent instance of this element type for the given patient. If there isn't one,
     * then returns $this
     *
     * @param Patient $patient
     * @param bool $use_context
     * @return BaseEventTypeElement
     */
    public function getMostRecentForPatient(\Patient $patient, $use_context = false)
    {
        return $this->getModuleApi()->getLatestElement(static::class, $patient, $use_context) ?: $this;
    }

    /**
     * @return BaseAPI
     */
    public function getModuleApi()
    {
        $event_type = $this->getElementType()->event_type;
        return $this->getApp()->moduleAPI->get($event_type->class_name);
    }

    /**
     * Stubbed method to set update options
     * Used by child objects to override null values for forms on update.
     */
    public function setUpdateOptions()
    {
    }

    public function getInfoText()
    {
    }

    public function getCreate_view()
    {
        return $this->getForm_View();
    }

    public function getForm_View()
    {
        return 'form_' . $this->getDefaultView();
    }

    public function getDefaultView()
    {
        $kls = explode('\\', get_class($this));

        return end($kls);
    }

    public function getUpdate_view()
    {
        return $this->getForm_View();
    }

    public function getPrint_view()
    {
        return $this->getView_View();
    }

    public function getView_view()
    {
        return 'view_' . $this->getDefaultView();
    }

    public function getDefaultContainerView()
    {
        return '//patient/element_container_view';
    }

    public function getContainer_view_view()
    {
        return '//patient/element_container_view';
    }

    public function getContainer_print_view()
    {
        return '//patient/element_container_print';
    }

    public function getContainer_create_view()
    {
        return $this->getContainer_form_view();
    }

    public function getContainer_form_view()
    {
        return '//patient/element_container_form';
    }

    public function getContainer_update_view()
    {
        return $this->getContainer_form_view();
    }

    public function isEditable()
    {
        return true;
    }

    /**
     *  returns the front-end attributes with errors.
     */
    public function getFrontEndErrors()
    {
        return $this->frontEndErrors;
    }

    public function setFrontEndError($attribute)
    {
        $this->frontEndErrors[] = $attribute;
    }

    public function requiredIfSide($attribute, $params)
    {
        if (($params['side'] === 'left' && $this->eye_id != 2) || ($params['side'] === 'right' && $this->eye_id != 1)) {
            if ($this->$attribute !== 0 && $this->$attribute == null) {
                if (!@$params['message']) {
                    $params['message'] = ucfirst($params['side']) . ' {attribute} cannot be blank.';
                }
                $params['{attribute}'] = $this->getAttributeLabel($attribute);

                $this->addError($attribute, strtr($params['message'], $params));
            }
        }
    }

    public function addError($attribute, $message)
    {
        $message = str_replace("{attribute}", $this->getAttributeLabel($attribute), $message);
        $this->frontEndErrors[] = $this->errorAttributeException(str_replace('\\', '_', get_class($this)) . '_' . $attribute, $message);
        $message = '<a class="errorlink" onClick="scrollToElement($(\'.' . str_replace(
            '\\',
            '_',
            get_class($this)
        ) . '\'))">' . $message . '</a>';
        parent::addError($attribute, $message);
    }

    public function clearErrors($attribute = null)
    {
        parent::clearErrors($attribute);
        if ($attribute === null) {
            $this->frontEndErrors = [];
        } else {
            $to_remove = $this->errorAttributeException(str_replace('\\', '_', get_class($this)) . '_' . $attribute, '');
            $this->frontEndErrors = array_filter($this->frontEndErrors, function ($key) use ($to_remove) {
                return $key !== $to_remove;
            });
        }
    }

    /**
     * Allows for exceptions where the element displayed is not the one required. eg for ajax control elements.
     *
     * @param $attribute
     * @param $message not used in the base implementation
     * @return mixed
     */
    protected function errorAttributeException($attribute, $message)
    {
        if (array_key_exists($attribute, $this->errorExceptions)) {
            return $this->errorExceptions[$attribute];
        }

        return $attribute;
    }

    /**
     * @param $attribute
     * @param $params
     *
     * requiredIfNoComments validator function
     * Checks if side matches and comments are non-empty
     */

    public function requiredIfNoComments($attribute, $params)
    {
        $comments_attribute = $params['side'] . '_' . $params['comments_attribute'];

        if (($params['side'] === 'left' && $this->eye_id != 2) || ($params['side'] === 'right' && $this->eye_id != 1)) {
            if ((($this->$attribute === '' || is_null($this->$attribute)) && ($this->$comments_attribute === '' || is_null($this->$comments_attribute)))) {
                if (!@$params['message']) {
                    $params['message'] = ucfirst($params['side']) . ' {attribute} cannot be blank.';
                }
                $params['{attribute}'] = $this->getAttributeLabel($attribute);

                $this->addError($attribute, strtr($params['message'], $params));
            }
        }
    }

    /**
     * stub method to allow elements to carry out actions related to being a part of a soft deleted event.
     */
    public function softDelete()
    {
    }

    /**
     * Returns true if the specified multiselect relation has the value $value_string.
     */
    public function hasMultiSelectValue($relation, $value_string)
    {
        foreach ($this->$relation as $item) {
            if ($item->name == $value_string) {
                return true;
            }
        }

        return false;
    }

    /**
     * Updates multiselect items in the database, deleting items not passed in $ids.
     */
    public function updateMultiSelectData($model, $ids, $relation_field)
    {
        $_ids = array();

        foreach ($ids as $id) {
            if (!$assignment = $model::model()->find("element_id=? and $relation_field=?", array($this->id, $id))) {
                $assignment = new $model();
                $assignment->element_id = $this->id;
                $assignment->$relation_field = $id;

                if (!$assignment->save()) {
                    throw new Exception('Unable to save assignment: ' . print_r($assignment->getErrors(), true));
                }
            }

            $_ids[] = $assignment->id;
        }

        $criteria = new CDbCriteria();
        $criteria->addCondition('element_id = :element_id');
        $criteria->params[':element_id'] = $this->id;

        !empty($_ids) && $criteria->addNotInCondition('id', $_ids);

        $model::model()->deleteAll($criteria);
    }

    /**
     * Store 1 or more audit messages if not already set for auditing
     *
     * @param $audit string or array of strings
     */
    public function addAudit($audit)
    {
        if ($audit && !is_array($audit)) {
            $audit = array($audit);
        }
        foreach ($audit as $a) {
            if (!in_array($a, $this->audit)) {
                $this->audit[] = $a;
            }
        }
    }

    /**
     * Return the display order of element, solve the problem elements has different order in different display mode.
     * @return mixed
     */
    public function getDisplayOrder()
    {
        return $this->getElementType()->display_order;
    }

    /**
     * Property getter
     * @return mixed
     */
    public function getDisplay_order()
    {
        return $this->getDisplayOrder();
    }

    /**
     * If the element should be managed with a widget, returns the class of the widget
     *
     * @return BaseEventElementWidget|null
     */
    public function getWidgetClass()
    {
        return $this->widgetClass;
    }

    /**
     * If an element has a method getPrefillableAttributeSet, it will be called to get a structure
     * to process and then use to extract the attribute data stored in the template.
     *
     * If that method does not exist in the element, null will be returned.
     * This permits elements to be ignored entirely.
     *
     * The structure returned by getPrefillableAttributeSet should be as follows:
     * - A string value (not key) naming an attribute in the element pulls that value directly from the element e.g. ['event_id'] yields '1'
     * - A string key mapping to a string or array of strings, with the key being a relation in the element and the value(s) being attributes in the relation.
     * An array of attribute names will retain those names. e.g. ['event' => 'id'] yields ['1', '2'], ['event' => ['id', 'created_user_id']] yields [['id' => 1, 'created_user_id' => 3]]
     * - A string key mapping to a function or an array with a key 'callable' mapping to a callable will invoke that function/callable with the element and the key
     * to allow for special processing
     *
     * With the second point, the relation is examined to see if it is an array or not and processed suitably.
     * e.g. 'event' => 'id' yielding '1', 'events' => 'id' yields ['1', '2'] where event is an object and events is an array of objects.
     *
     * @return array<string, mixed>|null
     */
    public function getPrefilledAttributes()
    {
        if (!method_exists($this, 'getPrefillableAttributeSet')) {
            return null;
        }

        $attributes = $this->getAttributes();
        $prefill_set = $this->getPrefillableAttributeSet();
        $prefilled = [];

        foreach ($prefill_set as $key => $value) {
            if (!is_string($key)) {
                $prefilled[$value] = $attributes[$value];
            } elseif (is_string($value)) {
                if (is_array($this->{$key})) {
                    $prefilled[$key] = array_map(static function ($item) use ($value) {
                        return $item->{$value};
                    }, $this->{$key});
                } else {
                    $prefilled[$key] = $this->{$key}->{$value};
                }
            } elseif (is_array($value)) {
                if (!empty($value['callable'])) {
                    $prefilled[$key] = call_user_func($value['callable'], $this, $key);
                } else {
                    $reducer = static function ($item) use ($value) {
                        return array_reduce(
                            $value,
                            static function ($result, $name) use ($item) {
                                $result[$name] = $item->{$name};

                                return $result;
                            },
                            []
                        );
                    };

                    $prefilled[$key] = is_array($this->{$key}) ? array_map($reducer, $this->{$key}) : $reducer($this->{$key});
                }
            } elseif (is_callable($value)) {
                $prefilled[$key] = call_user_func($value, $this, $key);
            } else {
                throw new Exception("Invalid prefill structure entry");
            }
        }

        return $prefilled;
    }

    public function getPrefilledAttributeNames()
    {
        if (!method_exists($this, 'getPrefillableAttributeSet')) {
            return [];
        }

        $names = [];

        foreach ($this->getPrefillableAttributeSet() as $key_name => $value_name) {
            if (!is_string($key_name)) {
                $names[] = $value_name;
            } else {
                $names[] = $key_name;
            }
        }

        return $names;
    }

    /**
     * @inheritdoc
     * @return bool
     */
    protected function beforeSave()
    {
        $this->checkForAudits();
        return parent::beforeSave();
    }

    /**
     * Stub method for audit checking before an element is saved.
     */
    protected function checkForAudits()
    {
    }

    /**
     * @inheritdoc
     */
    protected function afterSave()
    {
        parent::afterSave();
        $this->doAudit();
    }

    /**
     * Audit the stored audit items
     */
    protected function doAudit()
    {
        if (count($this->audit)) {
            $user = $this->getChangeUser();
            $patient = $this->event->getPatient();
            foreach ($this->audit as $a) {
                $user->audit('patient', $a, null, false, array('patient_id' => $patient->id));
            }
            $this->audit = array();
        }
    }

    /**
     * @return BaseEventTypeElement[] sibling elements in the same group
     */
    protected function getSiblings()
    {
        $siblings = array();

        foreach ($this->getSiblingTypes() as $siblingType) {
            if ($this->event_id) {
                if (class_exists($siblingType->class_name)) {
                    if ($element = self::model($siblingType->class_name)->find('event_id = ?', array($this->event_id))) {
                        $siblings[] = $element;
                    }
                } else {
                    Yii::log('Missing sibling element class ' . $siblingType->class_name . ' for event id ' . $this->event_id, 'Error');
                }
            }
        }

        return $siblings;
    }

    /**
     * @return ElementType[] Array of element types in the same group minus self
     */
    private function getSiblingTypes()
    {
        $element_type = $this->getElementType();
        return ElementType::model()->findAll(
            'element_group_id = :group_id AND id != :this_id',
            array(':group_id' => $element_type->element_group_id,
                ':this_id' => $element_type->id
            )
        );
    }
}
