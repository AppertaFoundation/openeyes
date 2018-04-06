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

class SubspecialtyFirmPicker extends \BaseFieldWidget
{
    public $subspecialties;
    public $firms = [];
    public $model;

    public $firm_id;
    public $subspecialty_id;

    public $layoutColumns = array(
        'label' => 2,
        'field' => 5,
    );

    public function init()
    {
        parent::init();
        $this->subspecialties = \Subspecialty::model()->findAll();
        if($this->model->subspecialty_id){
            $this->firms = \Firm::model()->getList($this->model->subspecialty_id);
        }
    }
}