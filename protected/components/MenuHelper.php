<?php
/**
 * Created by PhpStorm.
 * User: petergallagher
 * Date: 26/03/15
 * Time: 12:45.
 */
class MenuHelper
{
    protected $menuOptions;
    protected $user;
    protected $uri;
    /**
     * @var CApplication
     */
    protected $app;

    public function __construct(array $menuOptions, OEWebUser $user, $uri = '')
    {
        $this->menuOptions = $menuOptions;
        $this->user = $user;
        $this->uri = $uri;
    }

    /**
     * @param CApplication $app
     */
    public function setApp(CApplication $app)
    {
        $this->app = $app;
    }

    /**
     * @return CApplication
     */
    public function getApp()
    {
        if (!$this->app) {
            $this->app = Yii::app();
        }

        return $this->app;
    }

    /**
     * @return string
     */
    public function render($navIconUrl)
    {
        return $this->getApp()->controller->renderPartial(
            '//base/_menu',
            array(
                'menu' => $this->formatMenuOptions($this->menuOptions),
                'uri' => $this->uri,
                'navIconUrl' => $navIconUrl
            ),
            true
        );
    }

    /**
     * Map auth item param specifications to actual values to pass for access checking
     *
     * @param array $params
     * @return array
     */
    protected function getAuthItemParams($params = array())
    {
        $result = array();
        foreach($params as $p) {
            switch ($p)
            {
                case 'user_id':
                    $result[] = $this->user->id;
                    break;
                default:
                    $result[] = $p;
                    break;
            }
        }
        return $result;
    }

    /**
     * @param array $menuOptions
     * @return array
     */
    protected function formatMenuOptions(array $menuOptions, $position = 0)
    {
        $menu = array();

        foreach ($menuOptions as $menu_item) {
            if (isset($menu_item['restricted'])) {
                $allowed = false;
                foreach ($menu_item['restricted'] as $authitem) {
                    if (is_array($authitem)) {
                        $item = array_shift($authitem);
                        $params = $this->getAuthItemParams($authitem);
                        $allowed = $this->user->checkAccess($item, $params);
                        if ($allowed) {
                            break;
                        }

                    }
                    elseif ($this->user->checkAccess($authitem)) {
                        $allowed = true;
                        break;
                    }
                }
                if (!$allowed) {
                    if (isset($menu_item['userrule'])) {
                        if ($this->user->{$menu_item['userrule']}()) {
                            $allowed = true;
                        }
                    }
                    if (!$allowed) {
                        continue;
                    }
                }
            }

            /*
             * Check if menu item requires a system setting
             * in order to be displayed
             */

            if (isset($menu_item['requires_setting']))
            {
                $setting_key = $menu_item['requires_setting']['setting_key'];
                $required_value = $menu_item['requires_setting']['required_value'];

                $element_enabled = \SettingMetadata::model()->getSetting($setting_key);
                if (isset($element_enabled) && $element_enabled != $required_value)
                {
                    switch ($required_value) {
                        case 'not-empty':
                            if (!empty($element_enabled)) {
                                break;
                            }
                        default:
                            continue 2;
                    }
                }
            }

            if (isset($menu_item['api'])) {
                $api = Yii::app()->moduleAPI->get($menu_item['api']);
                foreach ($api->getMenuItems($menu_item) as $item) {
                    $menu[$position++] = $item;
                }
            } else {
                $menu[$position] = $menu_item;
            }

            if (isset($menu_item['sub'])) {
                $menu[$position]['sub'] = $this->formatMenuOptions($menu_item['sub'], $position);
            }

            $position++;
        }

        usort($menu, function ($a, $b) {
            return strcmp($a["title"], $b["title"]);
        });

        return $menu;
    }
}
