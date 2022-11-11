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

        // handle non-namespaced controller classes
        if (strrpos($cls, '\\') === false) {
            $cls = $this->importNonNamspacedClassWithUniqueAlias($cls);
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

    /**
     * This attempts to work around the fact that multiple modules have the same controller
     * class name in a different directory location by manipulating the file content as a
     * new unique class.
     *
     * @todo cache the unique class
     * @param string $cls
     * @return string
     */
    protected function importNonNamspacedClassWithUniqueAlias(string $cls): string
    {
        $path = \Yii::getPathOfAlias("application.modules.{$this->moduleCls}.controllers.$cls");
        $uniqueClassPostfix = rand();
        $source = file_get_contents("$path.php");
        $source = str_replace("class DefaultController ", "class DefaultController$uniqueClassPostfix ", $source);
        $tmp = tmpfile();
        fwrite($tmp, $source);
        require_once(stream_get_meta_data($tmp)['uri']);
        return "$cls$uniqueClassPostfix";
    }
}
