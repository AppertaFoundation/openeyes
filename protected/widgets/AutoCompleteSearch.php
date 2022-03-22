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
class AutoCompleteSearch extends BaseCWidget
{
        public $field_name = 'oe-autocompletesearch';
        public $hidden = false;

        // we can block js include in view file for templates <script type="text/template">
        public $js_include = true;

            /**
             * @var bool This variable can be passed when calling the Widget and it controls whether
             * to display the no results found and minimum characters warning message to the user.
             */
        public $hide_no_result_msg = false;

    public $htmlOptions = array(
        'placeholder' => 'Type to search'
    );
    public $layoutColumns = array(
        'label' => '',
        'field' => 'full',
    );
}
