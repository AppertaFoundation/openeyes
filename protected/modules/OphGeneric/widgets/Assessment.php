<?php
/**
 * OpenEyes.
 *
 * (C) Copyright Apperta Foundation, 2020
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


class Assessment extends BaseModuleWidget
{

    /**
     * @var \Patient
     */
    public $patient;

    /**
     * @var array of ActiveRecords
     */
    public $assessment;

    /**
     * @var \OEModule\OphGeneric\models\AssessmentEntry entry
     */
    public $entry;

    /**
     * @var \Eye::LEFT or \Eye::RIGHT
     * no BOTH
     */
    public $eye;

    public $view_file;

    /**
     * @var \EventType
     */
    public $event_type;

    /**
     * If multiple Assesment has been displayed we can set the key Assessment[0][id]...
     * @var int
     */
    public $key;

    /**
     * Side left or right;
     * @var string
     */
    public $side;
    /**
     * Row number
     * @var int
     */
    public $row;

    public $js = ["Assessment"];

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $view = $this->getView();
        $this->render($view);
    }

    /**
     * Determine the view file to use
     */
    protected function getView()
    {
        if ($this->view_file) {
            // manually overridden/set
            return $this->view_file;
        }

        $event_type_view = $this->event_type ? ("_" . str_replace(' ', '', $this->event_type->name)) : '';

        return get_class($this) . $event_type_view;
    }
}
