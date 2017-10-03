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
 * Extends CLinkPager to add a 'showing 1 to 100 of 187' line.
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
        $this->header = 'Showing '.$from.' to '.$to.' of '.$this->getItemCount();
    }
}
