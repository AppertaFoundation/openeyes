<?php

trait CreatesControllers
{
    public function beginCreatesControllers()
    {
        if (!isset($this->moduleCls)) {
            throw new \RuntimeException('property "moduleCls" must be set on test classes that use the CreatesControllers trait.');
        }

        \Yii::import("application.modules.{$this->moduleCls}.*");
    }

    public function getController($cls, $methods = null)
    {
        if ($methods === null) {
            $methods = ['getControllerPrefix'];
        } else {
            $methods = array_merge(['getControllerPrefix'], $methods);
        }

        $controllerId = strtolower(str_replace('Controller', '', $cls));

        $controller = $this->getMockBuilder($cls)
                    ->setConstructorArgs([$controllerId, Yii::app()->getModule($this->moduleCls)])
                    ->setMethods($methods)
                    ->getMock();

        $controller->method('getControllerPrefix')
            ->willReturn($controllerId);

        $controller->init();

        \Yii::app()->controller = $controller;
        return $controller;
    }
}
