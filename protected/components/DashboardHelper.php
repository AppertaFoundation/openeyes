<?php 

/**
 * Created by Mike Smith <mike.smith@camc-ltd.co.uk>.
 */

/**
 * Class DashboardHelper
 *
 * Helper class to render dashboards based on configuration. By default will look directly at the 'dashboard_items' key
 * of the Yii params if none are provided.
 *
 */
class DashboardHelper {

    /**
     * @var array
     */
    protected $items;
    /**
     * @var OEWebUser
     */
    protected $user;

    /**
     * Flag to toggle the drag and drop sorting controls for widgets
     * @TODO: this will be switched to true in the future when we set up controls for storing user preference for widgets
     * @var bool
     */
    public $sortable;

    /**
     * @param array $items expected to be of the form:
     *  [
     *      [
     *          'module' => 'ModuleName',
     *          'restricted' => <array of auth items that will grant access to this dashboard> (optional)
     *      ]
     *  ]
     *
     * @param OEWebUser $user
     */
    public function __construct(array $items = null, OEWebUser $user = null)
    {

        $this->items = $items;
        $this->user = $user;

        if (is_null($this->items)) {
            $this->items = Yii::app()->params['dashboard_items'] ?: array();
        }
        if (is_null($this->user)) {
            $this->user = Yii::app()->user;
        }

        // uses a config variable for ease of turning on the demo.
        $this->sortable = Yii::app()->params['dashboard_sortable'] ?: false;
    }

    /**
     * Renders the HTML snippet of the Dashboard
     *
     * @return mixed
     * @throws
     */
    public function render()
    {
        return Yii::app()->controller->renderPartial(
            '//base/_dashboard',
            array(
                'items' => $this->renderItems(),
                'sortable' => $this->sortable,
            ),
            true
        );
    }

    /**
     * @throws Exception for incorrect configuration for dashboard rendering
     */
    protected function renderItems()
    {
        $renders = array();

        foreach ($this->items as $item) {
            if (isset($item['restricted'])) {
                $allowed = false;
                foreach ($item['restricted'] as $authitem) {
                    if ($this->user->checkAccess($authitem)) {
                        $allowed = true;
                        break;
                    }
                }
                if (!$allowed) {
                    continue;
                }
            }

            $item_render = null;


            if ( isset($item['module']) )
            {
                $this->moduleRender($renders, $item);
            }
            else if ( isset($item['class']) && isset($item['method']) ) {
                $this->objRender($renders, $item);
            }
            else if ( isset($item['title']) && isset($item['content']) ) {
                // just a straight dump of the item structure into the render list
                $renders[] = $item;
            }
            else {
                throw new Exception("Invalid dashboard configuration: module, static or object definition required");
            }
        }

        return $renders;
    }

    /**
     * @param $renders
     * @param $item
     * @throws Exception
     */
    protected function moduleRender(&$renders, $item)
    {
        $module_name = $item['module'];

        $module = Yii::app()->moduleAPI->get($module_name);

        if (!$module) {
            throw new Exception("$module_name not found");
        }
        if( isset($item['actions']) && is_array($item['actions']) ) {
            foreach ($item['actions'] as $method_name) {
                if (!method_exists($module, $method_name))
                    throw new Exception("$method_name method not found for {$module_name}");
                $render = $module->$method_name();
                if ($render)
                    $renders[] = $render;
            }
        }
        else if( method_exists($module, 'renderDashboard') ) {
            $render = $module->renderDashboard();
            if ($render)
                $renders[] = $render;
        }
        else {
            throw new Exception('renderDashboard method not found for {$module_name}');
        }
    }

    /**
     * @param $renders
     * @param $item
     */
    protected function objRender(&$renders, $item)
    {
        $class_name = $item['class'];
        $method = $item['method'];
        $obj = new $class_name;
        $render = $obj->$method();
        if ($render)
            $renders[] = $render;

    }
}