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

    public $firstPageLabel = '';
    public $prevPageLabel = '';
    public $nextPageLabel = '';
    public $previousPageCssClass = 'oe-i arrow-left-bold medium pad';
    public $nextPageCssClass = 'oe-i arrow-right-bold medium pad';
    public $hiddenPageCssClass = 'disabled';
    public $includeFirstAndLastPageLabel = false;

    /**
     * Inits Pager.
     */
    public function init()
    {
        parent::init();
        $this->setHeaderToShowing();
    }

    /**
     * Setting the header information e.g: 1-5 of 15
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

        $this->header = $from . ' - ' . $to . ' of ' . $this->getItemCount();
    }

    /**
     * LinkPager overrides CLinkPager's run function to render the buttons as sequential a tags rather than list tags.
     */
    public function run()
    {
        $this->registerClientScript();

        echo \CHtml::openTag('div', ['class' => 'pagination']);

        echo $this->header;
        echo $this->createPrevPageButton();
        echo \CHtml::tag('span', ['class' => 'pagination-pages'], $this->createPageButtons(), true);
        echo $this->createNextPageButton();
        echo \CHtml::closeTag('div');
    }

    /**
     * Creating the previous (left) arrow button
     * @return string
     */
    private function createPrevPageButton()
    {
        $currentPage = $this->getCurrentPage(false);
        $page = max($currentPage - 1, 0);

        return $this->createPageButton($this->prevPageLabel, $page, $this->previousPageCssClass, $currentPage <= 0, false);
    }

    /**
     * Creates a page button.
     * You may override this method to customize the page buttons.
     * @param string $label the text label for the button
     * @param integer $page the page number
     * @param string $class the CSS class for the page button.
     * @param boolean $hidden whether this page button is visible
     * @param boolean $selected whether this page button is selected
     * @return string the generated button
     */
    protected function createPageButton($label, $page, $class, $hidden, $selected)
    {
        if ($hidden || $selected) {
            $class .= ' ' . ($hidden ? $this->hiddenPageCssClass : $this->selectedPageCssClass);
        }

        $currentAppendBehaviour = Yii::app()->urlManager->appendParams;
        Yii::app()->urlManager->appendParams = false;
        $button = CHtml::link($label, $selected ? '' : $this->createPageUrl($page), ['class' => $class]);
        Yii::app()->urlManager->appendParams = $currentAppendBehaviour;
        return $button;
    }

    /**
     * Create the internal buttons (page numbers between the arrows)
     * @return string Previous/next buttons
     */
    protected function createPageButtons()
    {
        if (($pageCount = $this->getPageCount()) <= 1) {
            return '';
        }
        list($beginPage, $endPage) = $this->getPageRange();
        $currentPage = $this->getCurrentPage(false);

        if ($this->includeFirstAndLastPageLabel && $beginPage !== 0) {
            $buttons[] = $this->createFirstPageButton();
        }

        // internal pages
        for ($i = $beginPage; $i <= $endPage; ++$i) {
            $buttons[] = $this->createPageButton($i + 1, $i, $this->internalPageCssClass, false, $i == $currentPage);
        }

        if ($this->includeFirstAndLastPageLabel && $endPage !== $pageCount - 1) {
            $buttons[] = $this->createLastPageButton();
        }

        return implode('', $buttons);
    }

    /**
     * Creating the next (right) arrow button
     * @return string
     */
    private function createNextPageButton()
    {
        $currentPage = $this->getCurrentPage(false);
        $pageCount = $this->getPageCount();
        $page = $currentPage + 1;

        if ($page >= $pageCount - 1) {
            $page = $pageCount - 1;
        }

        return $this->createPageButton($this->nextPageLabel, $page, $this->nextPageCssClass, $currentPage >= $pageCount - 1, false);
    }

    private function createFirstPageButton()
    {
        $currentPage = $this->getCurrentPage(false);
        return $this->createPageButton('1', 0, $this->internalPageCssClass, $currentPage == 0, false) . '...';
    }

    private function createLastPageButton()
    {
        $currentPage = $this->getCurrentPage(false);
        $pageCount = $this->getPageCount();
        return '...' . $this->createPageButton("{$pageCount}", $pageCount - 1, $this->internalPageCssClass, $currentPage >= $pageCount - 1, false);
    }
}
