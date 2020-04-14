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
 *
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
    public $image_groups = [];
    private $num_pages = 0;

    public function init()
    {
        if (isset($this->images[0]->document_number)) {
            foreach ($this->images as $image) {
                $this->image_groups[$image->document_number][] = $image;
            }
        }
        $this->num_pages = count($this->images);
    }

    /**
     * @throws CException
     */
    public function run()
    {
        $asset_manager = Yii::app()->getAssetManager();
        $widget_path = $asset_manager->publish('protected/widgets/js/MultipageView.js', true);
        Yii::app()->clientScript->registerScriptFile($widget_path);
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
