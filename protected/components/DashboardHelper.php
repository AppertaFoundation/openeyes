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

            if (isset($item['api'])) {
                $api = Yii::app()->moduleAPI->get($item['api']);
                if (!$api) {
                    throw new Exception("API not found");
                }
                $renders[] = $api->renderDashboard();
            }
            else {
                throw new Exception("Invalid dashboard configuration, api definition required");
            }
        }
        return $renders;
    }
}