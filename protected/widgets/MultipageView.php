<?php

/**
 * Class MultipageView
 * @property $stack_class string|null Additional HTML class/es to add to the page stack container.
 * @property $full_width bool If true, display the images using the full width of the parent container.
 * @property $inline_nav bool If true, display nav controls as an overlay over the stack. Otherwise, display in a sidebar.
 * @property $class string|null Class name for the containing element.
 * @property $id string|null HTML ID of the containing element.
 * @property $element string|null HTML element to contain the widget. Defaults to null, meaning the widget will not render within a container.
 * @property $nav_title string|null The heading to display above the navigation controls when rendered in the sidebar.
 * @property $images EventImage[] List of images to render.
 */
class MultipageView extends CWidget
{
    public $stack_class;
    public $full_width = false;
    public $inline_nav = false;
    public $class;
    public $id;
    public $element;
    public $nav_title;
    public $images = array();
    private $num_pages = 0;

    public function init()
    {
        $this->num_pages = count($this->images);
    }

    /**
     * @throws CException
     */
    public function run()
    {
        $assetManager = Yii::app()->getAssetManager();
        $widgetPath = $assetManager->publish('protected/widgets/js');
        Yii::app()->clientScript->registerScriptFile($widgetPath . '/MultipageView.js');
        if ($this->element) {
            $this->render('multipage/_container', array(
                'total_pages' => $this->num_pages
            ));
        } else {
            $this->render('multipage/_nav', array(
                'total_pages' => $this->num_pages
            ));
            $this->render('multipage/_stack');
        }
    }

    public function getTotalPages()
    {
        return $this->num_pages;
    }
}
