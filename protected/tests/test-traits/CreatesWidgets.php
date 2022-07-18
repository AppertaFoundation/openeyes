<?php

/**
 * Trait CreatesWidgets
 *
 */
trait CreatesWidgets
{
    protected $widgetFactory;
    protected $controller;
    protected $mockedApp;

    public function __get($name)
    {
        if (in_array($name, ['element_cls', 'widget_cls', 'controller_cls'])) {
            throw new \RuntimeException("you must set the $name property on this test class");
        }
        return parent::__get($name);
    }

    public function getWidgetFactory()
    {
        if (!$this->widgetFactory) {
            $this->widgetFactory = new \WidgetFactory();
        }
        return $this->widgetFactory;
    }

    public function mockApp()
    {
        if (!$this->mockedApp) {
            $this->mockedApp = $this->getMockBuilder(CWebApplication::class)
                ->disableOriginalConstructor()
                ->getMock();
            $this->mockedApp->method('getAssetManager')
                ->willReturn($this->createMock(CAssetManager::class));
        }

        return $this->mockedApp;
    }

    /**
     * Add module api mocks by module name key to the mock app
     * N.B. the mockApp will only apply in the widget that is created
     * if it supports the setApp method (and uses this paradigm for app retrieval)
     */
    protected function addModuleAPIToMockApp(array $mockMap = [])
    {
        $mockApp = $this->mockApp();
        $moduleAPIMock = $this->getMockBuilder(\ModuleAPI::class)
            ->disableOriginalConstructor()
            ->getMock();

        $valueMap = [];
        foreach ($mockMap as $moduleName => $apiMock) {
            // insert data context 'null' parameter to match ModuleAPI get method signature
            $valueMap[] = [$moduleName, null, $apiMock];
        }

        $moduleAPIMock->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($valueMap));

        $mockApp->expects($this->any())
            ->method('__get')
            ->with($this->equalTo('moduleAPI'))
            ->willReturn($moduleAPIMock);
    }

    public function getController()
    {
        if (!$this->controller) {
            // disable the constructor, but otherwise leave the behaviour alone
            $this->controller = $this->getMockBuilder($this->controller_cls)
                ->disableOriginalConstructor()
                ->setMethods(null)
                ->getMock();
        }
        return $this->controller;
    }

    protected function createWidgetWithProps($widget_cls, $props = [])
    {
        Yii::app()->setController($this->getController());

        $widget = $this->getWidgetFactory()->createWidget(
            $this->getController(),
            $widget_cls,
            $props
        );

        Yii::app()->setController($this->getController());

        if (method_exists($widget, 'setApp')) {
            $widget->setApp($this->mockApp());
        }

        $widget->init();

        return $widget;
    }

    /**
     * @param \BaseEventTypeElement|null $element
     * @param array $data
     * @return mixed
     */
    protected function getWidgetInstanceForElement($element = null, $data = null, $patient = null)
    {
        if ($element === null) {
            $cls = $this->element_cls;
            $element = new $cls();
        }

        if ($patient === null) {
            $patient = new \Patient();
        }

        return $this->createWidgetWithProps($this->widget_cls, [
            'element' => $element,
            'patient' => $patient,
            'data' => $data,
            'form' => $this->getWidgetFactory()
                ->createWidget(
                    $this->getController(),
                    BaseEventTypeCActiveForm::class
                )
        ]);
    }

    protected function getWidgetRender($widget)
    {
        ob_start();
        $widget->run();
        return ob_get_clean();
    }
}
