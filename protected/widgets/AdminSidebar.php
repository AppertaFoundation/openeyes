<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class AdminSidebar extends BaseCWidget
{
    /**
     * Holds the menu items
     * @var array
     */
    public $menu_items = [];

    public function init()
    {
        $this->menu_items = $this->getMenuItems(Yii::app()->params['admin_structure'] + ModuleAdmin::getAll());
        parent::init();
    }

    /**
     * Generates the menu items array
     * @param $items
     * @return array
     */
    public function getMenuItems($items)
    {
        $menu_items = [];
        foreach ($items as $box_title => $box_data) {
            // check the current title in the exclude_admin_structure_param_list array, if found then skip that element.
            if (Yii::app()->params['exclude_admin_structure_param_list'] !== null && in_array($box_title, Yii::app()->params['exclude_admin_structure_param_list'])) {
                continue;
            }
            foreach ($box_data as $item_title => $item) {
                $has_access = true;
                $uri = $item;
                if (is_array($item)) {
                    $uri = $item['uri'];
                    if (isset($item['restricted'])) {
                        $has_access = false;
                        $restricted = $item['restricted'];
                        foreach ($restricted as $role) {
                            // User only needs to have at least 1 of the specified roles to have access.
                            if (Yii::app()->controller->checkAccess($role)) {
                                $has_access = true;
                                break;
                            }
                        }
                    }

                    // need to check if function depends on a module
                    if (array_key_exists('module', $item)) {
                        // simply replaces all occurrences of context_firm_label with output of Firm::contextLabel
                        if (!array_key_exists($item['module'], Yii::app()->modules)) {
                            $uri = null;
                        }
                    }

                    if (array_key_exists('parameter', $item)) {
                        if (!isset(Yii::app()->params[$item['parameter']]) || !Yii::app()->params[$item['parameter']]) {
                            $uri = null;
                        }
                    }
                }

                // Nasty hack because Firm::contextLabel() is not available within config/core/admin.php
                $item_title = str_replace('context_firm_label', Firm::contextLabel(), $item_title);
                $item_title = str_replace('service_firm_label', Firm::serviceLabel(), $item_title);
                // end of nasty hack

                if ($has_access) {
                    $menu_items[$box_title][$item_title] = $uri;
                }
            }
        }
        return $menu_items;
    }

    /**
     * Checkes the cookies and returns the state of a group
     * @param $box_title
     * @return string
     */
    public function getGroupState($box_title)
    {
        $state = 'collapsed';

        foreach ($this->menu_items as $_box_title => $box_items) {
            foreach ($box_items as $_name => $data) {
                if ($box_title === $_box_title && Yii::app()->getController()->request->requestUri == $data ||
                    isset(\Yii::app()->controller->group) && \Yii::app()->controller->group == $box_title) {
                    return 'expanded';
                }
            }
        }

        return $state;
    }

    public function getCurrentTitle(): string
    {
        $current_path = '/' . Yii::app()->getRequest()->getPathInfo();
        $current_full_url = Yii::app()->getRequest()->getRequestUri();

        foreach ($this->menu_items as $box_items) {
            foreach ($box_items as $title => $admin_link) {
                if ($current_full_url === $admin_link) {
                    return $title;
                }

                if ($current_path === $admin_link) {
                    return $title;
                }
            }
        }

        return '';
    }
}
