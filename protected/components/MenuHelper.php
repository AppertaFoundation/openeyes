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

    public function __construct(array $menuOptions, OEWebUser $user, $uri = '')
    {
        $this->menuOptions = $menuOptions;
        $this->user = $user;
        $this->uri = $uri;
    }

    public function render()
    {
        return Yii::app()->controller->renderPartial(
            '//base/_menu',
            array(
                'menu' => $this->formatMenuOptions($this->menuOptions),
                'uri' => $this->uri,
            ),
            true
        );
    }

    protected function formatMenuOptions(array $menuOptions)
    {
        $menu = array();

        foreach ($menuOptions as $menu_item) {
            if (isset($menu_item['restricted'])) {
                $allowed = false;
                foreach ($menu_item['restricted'] as $authitem) {
                    if ($this->user->checkAccess($authitem)) {
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

            if (isset($menu_item['api'])) {
                $api = Yii::app()->moduleAPI->get($menu_item['api']);
                foreach ($api->getMenuItems($menu_item['position']) as $item) {
                    $menu[$item['position']] = $item;
                }
            } else {
                $menu[$menu_item['position']] = $menu_item;
            }

            if (isset($menu_item['sub'])) {
                $menu[$menu_item['position']]['sub'] = $this->formatMenuOptions($menu_item['sub'], $this->user);
            }
        }
        ksort($menu);

        return $menu;
    }
}
