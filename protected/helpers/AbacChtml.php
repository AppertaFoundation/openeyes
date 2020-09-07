<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php

class AbacChtml extends CHtml
{
    public static function activeCheckBox($model, $attribute, $htmlOptions = array())
    {
        $attribute_name = self::getAttributeName($model, $attribute);
        if ($model->abac_json) {
            $abac_json = json_decode($model->abac_json);

//            if (isset($abac_json->$attribute_name)) {
                // Has ABAC attribute assigned from JSON
                // TODO show display based on the ABAC value
//            }
        }
        return parent::activeCheckBox($model, $attribute, $htmlOptions);
    }

    public static function activeTextField($model, $attribute, $htmlOptions = array())
    {
        $attribute_name = self::getAttributeName($model, $attribute);
        if ($model->abac_json) {
            $abac_json = json_decode($model->abac_json);

//            if (isset($abac_json->$attribute_name) && $abac_json->$attribute_name === "RO") {
                // Has ABAC attribute assigned from JSON
                // TODO show display based on the ABAC value
//            }
        }
        return parent::activeTextField($model, $attribute, $htmlOptions);
    }


    public static function getAttributeName($model, $attribute)
    {
        if (($pos = strpos($attribute, '[')) !== false) {
            if ($pos === 0) { // [a]name[b][c], should ignore [a]
                if (preg_match('/\](\w+(\[.+)?)/', $attribute, $matches)) {
                    $attribute = $matches[1]; // we get: name[b][c]
                }
                if (($pos = strpos($attribute, '[')) === false) {
                    return $attribute;
                }
            }
            $name = substr($attribute, 0, $pos);
            return $name;
        } else {
            return $attribute;
        }
    }
}