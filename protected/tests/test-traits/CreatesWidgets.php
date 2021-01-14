<?php


/**
 * Trait CreatesWidgets
 *
 * @package OEModule\OphCiExamination\tests\unit\widgets\traits
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
                ->setMethods(['getBasePath', 'getAssetManager'])
                ->getMock();
            $this->mockedApp->method('getAssetManager')
                ->willReturn($this->createMock(CAssetManager::class));
        }

        return $this->mockedApp;
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
    protected function getWidgetInstanceForElement($element = null, $data = null)
    {
        if ($element === null) {
            $cls = $this->element_cls;
            $element = new $cls();
        }

        return $this->createWidgetWithProps($this->widget_cls, [
            'element' => $element,
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