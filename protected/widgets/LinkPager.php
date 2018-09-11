<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * Extends CLinkPager to add a 1 - 100 of 187' line.
 */
class LinkPager extends CLinkPager
{
    /**
     * Inits Pager.
     */
    public function init()
    {
        parent::init();
        $this->setHeaderToShowing();
    }

    /**
     * Sets the header to be our `showing...` string.
     */
    protected function setHeaderToShowing()
    {
        $page = $this->getCurrentPage() + 1; //0 indexed page so add one
        $to = $this->pages->getPageSize() * $page;
        if ($to > $this->getItemCount()) {
            $to = $this->getItemCount();
        }
        $from = $page;

        if ($page > 1) {
            $from = (($page - 1) * $this->pages->getPageSize()) + 1;
        }

        $this->header = $from.' - '.$to.' of '.$this->getItemCount();
    }

    /**
     * LinkPager overrides CLinkPager's run function to render the next/previous buttons as sequential i tags rather than list tags.
     */
    public function run()
    {
        $this->registerClientScript();
        $buttons=$this->createPageButtons();
        if(empty($buttons)) {
            echo $this->header;
            return;
        }

        echo $this->header;
        echo implode("\n",$buttons); // There will only ever be a maximum of two buttons when using this pager (next/previous).
        echo $this->footer;
    }

    /**
     * Create the next/previous buttons
     * @return array Previous/next buttons
     */
    protected function createPageButtons()
    {   $pageCount=$this->getPageCount();

        $currentPage=$this->getCurrentPage(false); // currentPage is calculated in getPageRange()
        $buttons=array();

        // prev page
        if(($page=$currentPage-1)<0)
            $page=0;
        $buttons[]=$this->createPageButton($this->prevPageLabel,$page,$currentPage <= 0 ? $this->previousPageCssClass.' disabled': $this->previousPageCssClass,false,false);

        // next page
        if(($page=$currentPage+1)>=$pageCount-1)
            $page=$pageCount-1;
        $buttons[]=$this->createPageButton($this->nextPageLabel,$page, $currentPage>=$pageCount-1 ? $this->nextPageCssClass.' disabled' : $this->nextPageCssClass,false,false);

        return $buttons;
    }

    /**
     * @param string $label Button label (unused)
     * @param int $page Page number (unused)
     * @param string $class Button class
     * @param bool $hidden Indicates if the button is hidden from sight
     * @param bool $selected Indicates if the button has been selected.
     * @return string HTML for the given button.
     */
    protected function createPageButton($label,$page,$class,$hidden,$selected)
    {
        if ($hidden || $selected)
            $class .= ' ' . ($hidden ? $this->hiddenPageCssClass : $this->selectedPageCssClass);
        return CHtml::link('<i class="' . $class . '"></i>', $this->createPageUrl($page),
            strpos($class, 'disabled') ?
                array('style' => '
            cursor: default;
            pointer-events: none;') :
                array());
    }
}
