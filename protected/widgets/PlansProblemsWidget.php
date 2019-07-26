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


class PlansProblemsWidget extends BaseCWidget
{
    public $allow_save = true;
    public $patient_id;
    public $plans_problems;
    public $pro_theme = '';

    public function init()
    {
        parent::init();

        $criteria = new CDbCriteria();
        $criteria->addCondition("patient_id=:patient_id");
        $criteria->params[":patient_id"] = $this->patient_id;

        $this->plans_problems = PlansProblems::model()->display_order()->findAll($criteria);
    }
}
