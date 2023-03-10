<?php

namespace OE\factories\models\traits;

/**
 * This trait should be attached to any factory that needs to build out a display_order
 * attribute when creating an instance. By default it will determine the current maximum
 * value and increment that by one for each model generated.
 *
 * If that factory using it as a $display_order_attribute defined, that value will be used
 * as the column for calculating and applying the value to the models generated.
 */
trait MapsDisplayOrderForFactory
{
    protected ?int $current_max_display_order = null;

    protected function getDisplayOrderAttribute(): string
    {
        return property_exists($this, 'display_order_attribute')
            ? $this->display_order_attribute
            : 'display_order';
    }

    protected function mapDisplayOrderAttributes(array $instances): array
    {
        return array_map(
            function ($instance) {
                if ($instance->{$this->getDisplayOrderAttribute()} === null) {
                    $instance->{$this->getDisplayOrderAttribute()} = $this->getNextDisplayOrderValue();
                }
                return $instance;
            },
            $instances
        );
    }

    protected function getNextDisplayOrderValue(): int
    {
        return $this->incrementCurrentMaxDisplayOrder();
    }

    protected function incrementCurrentMaxDisplayOrder()
    {
        if ($this->current_max_display_order === null) {
            $this->current_max_display_order = $this->app
                ->getComponent('db')
                ->createCommand()
                ->select('MAX(' . $this->getDisplayOrderAttribute() . ')')
                ->from($this->modelName()::model()->tableName())
                ->queryScalar() ?? 0;
        }
        return ++$this->current_max_display_order;
    }
}
