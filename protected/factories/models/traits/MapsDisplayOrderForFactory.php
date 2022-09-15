<?php

namespace OE\factories\models\traits;

trait MapsDisplayOrderForFactory
{
    protected ?int $current_max_display_order = null;

    protected function mapDisplayOrderAttributes(array $instances): array
    {
        return array_map(
            function ($instance) {
                if ($instance->display_order === null) {
                    $instance->display_order = $this->getNextDisplayOrderValue();
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
                ->select('MAX(display_order)')
                ->from($this->modelName()::model()->tableName())
                ->queryScalar() ?? 0;
        }
        return ++$this->current_max_display_order;
    }
}
