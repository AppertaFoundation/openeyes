<?php

class EventDefaults extends CApplicationComponent
{
    protected EventType $event_type;
    protected array $elements;
    protected array $context;

    public function forEventType(EventType $event_type): EventDefaults
    {
        $this->event_type = $event_type;
        $this->elements = array();
        return $this;
    }

    public function forElements(array $elements): EventDefaults
    {
        foreach ($elements as $element) {
            if ($element instanceof BaseElement) {
                // Is element instance
                if ($element->canHaveMultipleOf()) {
                    $this->elements[$element->elementType->class_name][] = $element;
                } else if (!array_key_exists($element->elementType->class_name, $this->elements)) {
                    $this->elements[$element->elementType->class_name] = $element;
                }
            } else {
                // Create elements from other objects that have an element_type property/relation
                if (is_object($element) && !empty($element->element_type)) {
                    $element = $element->element_type->class_name;
                }

                // Is string
                if ($element::canHaveMultipleOf()) {
                    $this->elements[$element][] = new $element();
                } else if (!array_key_exists($element, $this->elements)) {
                    $this->elements[$element] = new $element();
                }
            }
        }
        
        return $this;
    }
    
    public function getEventType(): EventType
    {
        return $this->event_type;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getDefaults(): array
    {
        $defaults = array();

        foreach ($this->elements as $element) {
            if (is_array($element)) {
                $defaults[$element[0]->elementType->class_name] = array_map(function($element) { return $element->getDefaults($this->context); }, $element);
            } else {
                $defaults[$element->elementType->class_name] = $element->getDefaults($this->context);
            }
        }

        return $defaults;
    }

    public function withContext(array $context): EventDefaults
    {
        $this->context = $context;
        return $this;
    }
}
