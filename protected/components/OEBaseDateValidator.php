<?php

/**
 * OpenEyes.
 *
 * Copyright OpenEyes Foundation, 2017
 *
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
abstract class OEBaseDateValidator extends CValidator
{
    public $include_time = false;
    public $time_required = false;

    public function parseDateValue($value)
    {
        $res = false;
        if (preg_match('/^\d\d\d\d-\d\d-\d\d( \d\d:\d\d(:\d\d){0,1}){0,1}$/', $value, $matches)) {
            switch (count($matches)) {
                case 1:
                    if (!$this->time_required) {
                        $res = DateTime::createFromFormat('Y-m-d H:i:s', $value.' 00:00:00');
                    }
                    break;
                case 2:
                    $res = DateTime::createFromFormat('Y-m-d H:i:s', $value.':00');
                    break;
                case 3:
                    $res = DateTime::createFromFormat('Y-m-d H:i:s', $value);
                    break;
                default:
                    $res = false;
            }
        }
        // check there were no warnings because of invalid date values (strict checking)
        if ($res) {
            $errs = DateTime::getLastErrors();
            if (!empty($errs['warning_count'])) {
                $res = false;
            }
        }

        return $res;
    }
}
