<?php

interface WidgetizedElement
{
    /**
     * @return string
     * The Widget class must be an instance of BaseEventElementWidget
     */

    public function getWidgetClass();

    /**
     * @return BaseEventElementWidget
     */

    public function getWidget();

    public function setWidget(BaseEventElementWidget $widget);
}