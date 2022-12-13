<?php
/**
 * Class EventBuilder
 */
class EventBuilder extends CApplicationComponent
{
    public const PRIORITY_IGNORE = 0;
    public const PRIORITY_ALWAYS = -1;

    protected array $elements = array();
    protected array $priorities = array();
    protected EventType $eventType;
    protected array $data = array();

    /**
     * Specifies the event type to use for event construction.
     * Clears any elements and data being stored
     *
     * @param EventType $event_type
     * @return static
     */
    public function forEventType(EventType $event_type): EventBuilder
    {
        $this->eventType = $event_type;
        $this->elements = [];
        $this->priorities = [];
        $this->data = [];

        return $this;
    }

    public function getEventType(): EventType
    {
        return $this->eventType;
    }

    /**
     * Adds a whitelist of element types.
     *
     * @param ElementType[]|string[] $elements
     * @return static
     */
    public function forElements(array $elements): EventBuilder
    {
        foreach ($elements as $element) {
            if ($element instanceof BaseElement) {
                // Is element instance
                $index = $element->templateIndex ?? count($this->elements[$element->elementType->class_name] ?? []);

                $this->elements[$element->elementType->class_name][$index] = $element;
            } else {
                // Create elements from other objects that have an element_type property/relation
                if (is_object($element) && !empty($element->element_type)) {
                    $element = $element->element_type->class_name;
                }

                // Is string
                $this->elements[$element][] = new $element();
            }
        }
        
        return $this;
    }

    /**
     * Allow for a set of priorities to be passed to the EventBuilder
     * to guide the addition of element data.
     *
     * The $priorites array should be structured as an associative array of associative arrays:
     * [element_class_name => [field_name => priority, field_name => priority ...], element_class_name => ...]
     * - The element_class_name keys will match with those used in the elements (see forElements above)
     * - The field names match those that will be passed in as the data for the elements
     * - The priority is either one of the constants defined above or one defined externally for the appropriate usecase,
     *   e.g. in EventTemplate for use with patient data or new data in event templating.
     *
     * @param array $priorities
     * @return static
     */
    public function setPriorities(array $priorities): EventBuilder
    {
        $this->priorities = $priorities;

        return $this;
    }

    /**
     * Collects external data to be added to all whitelisted event elements.
     *
     * If there is not data, the EventBuilder will store the entirety of what it is given.
     * Otherwise, it will treat the incomming data as an associative array with keys pointing
     * to associative arrays containing the actual data.
     *
     * Any new top level array entries are stored as is; any that shares an existing key will
     * store any new data in associative array one level deeper.
     *
     * Existing data is only overriden when a suitable priority condition is met.
     * Priorities work as follows:
     * - An associative array is passed in via setPriorities with keys and values as described in the comments above.
     * - The current priority for the data being added is passed in as the $priority parameter.
     *   This comes either from the broad options defined above or as defined for the specific usecase where needed, e.g. in EventTemplate
     * - If a priority is set at the appropriate level in the values passed to setPriority above, e.g. $this->priorities[$toplevel_key][$inner_key],
     *   then it is either compared with the priority parameter passed to addData or checked to see if it is a callable value.
     * - If it is equal to the priority parameter, replace the existing value with the new value.
     * - If it is callable, always call it with the existing data, new data and the priority parameter passed to addData,
     *   replacing the existing data with the data returned from the callback.
     * - The existing data is not replaced if either of the above two conditions are not met.
     *
     * @param array $data
     * @return static
     */
    public function addData(array $data, int $priority = self::PRIORITY_IGNORE): EventBuilder
    {
        if (empty($this->data)) {
            $this->data = $data;
        } else {
            foreach ($data as $toplevel_key => $toplevel_value) {
                if (!array_key_exists($toplevel_key, $this->data)) {
                    $this->data[$toplevel_key] = $toplevel_value;
                } else if (is_array($this->data[$toplevel_key]) && is_array($toplevel_value)) {
                    $priorities = $this->priorities[$toplevel_key] ?? [];

                    foreach ($toplevel_value as $inner_key => $inner_value) {
                        $new_priority = array_key_exists($inner_key, $priorities) ? $priorities[$inner_key] : null;

                        $override = $new_priority && $priority !== self::PRIORITY_IGNORE &&
                                  (
                                      $new_priority === self::PRIORITY_ALWAYS ||
                                      $new_priority === $priority ||
                                      is_callable($new_priority)
                                  );
                        $is_missing = !array_key_exists($inner_key, $this->data[$toplevel_key]);

                        if ($is_missing || $override) {
                            if (!$is_missing && is_callable($new_priority)) {
                                $this->data[$toplevel_key][$inner_key] = call_user_func($new_priority, $this->data[$toplevel_key][$inner_key], $inner_value, $priority);
                            } else {
                                $this->data[$toplevel_key][$inner_key] = $inner_value;
                            }
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Applies collected data to all whitelisted event elements.
     *
     * @return static
     */
    public function applyData(): EventBuilder
    {
        foreach ($this->eventType->getAllElementTypes() as $elementType) {
            // need to handle removal flag in data
            $fieldName = CHtml::modelName($elementType->class_name);

            if (array_key_exists($fieldName, $this->data)) {
                $this->applyElementData($elementType, $this->data);
            }
        }
        return $this;
    }

    /**
     * Apply data to a specific element.
     *
     * @param ElementType $elementType
     * @param array $elementData
     * @return void
     */
    protected function applyElementData(ElementType $elementType, array $elementData): void
    {
        $elementInstances = $this->getElementsByType($elementType);

        if ($elementInstances) {
            foreach ($elementInstances as $index => $instance) {
                if ($instance->canHaveMultipleOf()) {
                    $instance->applyData($elementData, $index);
                } else {
                    $instance->applyData($elementData, null);
                }
            }
        }
    }

    /**
     * Gets a single element
     *
     * @param ElementType $elementType
     * @param int $index
     * @return BaseElement
     */
    protected function getElement(ElementType $elementType, $index = 0): BaseElement
    {
        if (!array_key_exists($elementType->class_name, $this->elements) || !$this->elements[$elementType->class_name][$index]) {
            $this->elements[$elementType->class_name][$index] = $elementType->getInstance();
        }

        return $this->elements[$elementType->class_name][$index];
    }

    /**
     * Gets all the elements for a specific element type
     *
     * @param ElementType $elementType
     * @param int $index
     * @return BaseElement
     */
    protected function getElementsByType(ElementType $elementType): array
    {
        return $this->elements[$elementType->class_name] ?? [];
    }

    /**
     * Gets all elements in the event builder.
     *
     * @return array
     */
    public function getElements(): array
    {
        return array_reduce($this->elements, 'array_merge', []);
    }
}
