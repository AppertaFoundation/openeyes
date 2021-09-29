<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * choices can be defined for providing a dropdown in a row that doesn't come from a relationship lookup.
 * The definition follows a similar pattern to that of lookup, but instead of providing a model, a choices
 * attribute should be provided:
 *
 *  "identifier" => [
 *      "field" => "attribute_name",
 *      "type" => "choice",
 *      "allow_null" => false,
 *      "choices" => [
 *          storedValue => "Displayed Value",
 *      ]
 *  ]
 */

$htmlOptions = @$params['htmlOptions'] ?: array();

if (@$disabled) {
    $htmlOptions['disabled'] = 'disabled';
}
if ($params['allow_null']) {
    $htmlOptions['empty'] = '-';
}
$value = $row ? $row->{$params['field']} : null;
echo CHtml::dropDownList($params['field']."[{$i}]", $value, $params['choices'], $htmlOptions);
