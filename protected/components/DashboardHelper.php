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
     * @param array $items expected to be of the form:
     *  [
     *      [
     *          'api' => 'ModuleName',
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
                'items' => $this->renderItems()
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
            
            if ( isset($item['module']) )
            {
                $module_name = $item['module'];

                $module = Yii::app()->moduleAPI->get($module_name);

                if (!$module) {
                    throw new Exception("$module_name not found");
                }

                if( isset($item['actions']) && is_array($item['actions']) ) {
                    $renders = $this->renderActions($module, $item['actions']);
                }
                else if( method_exists($module, 'renderDashboard') ) {
                    $renders[] = $module->renderDashboard();
                }
                
            } else if ( isset($item['title']) && isset($item['content']) ) {
                $renders[] = $item;
            }
        }
        return $renders;
    }
    
    protected function renderActions($module, $actions)
    {
        $renders = array();
        
        foreach($actions as $method_name)
        {
            if( method_exists($module, $method_name) )
            {
                $renders[] = $module->$method_name();
            } else
            {
                throw new Exception("$method_name method not found");
            }
        }
        
        return $renders;
    }
}