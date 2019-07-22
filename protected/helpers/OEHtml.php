<?php
/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

class OEHtml extends CHtml
{
    public static function icon($text, $htmlOption = [])
    {
        return self::tag("i", array_merge($htmlOption, [
            "class" => "oe-i $text"  . (isset($htmlOption["class"]) ? " " . $htmlOption["class"] : ""),
        ]), '', true);
    }

    public static function button($text, $htmlOption = [])
    {
        return \CHtml::button(
            $text,
            array_merge([
                "class" => "button large",
            ], $htmlOption)
        );
    }

    public static function submitButton($text = "Save", $htmlOption = [])
    {
        return \CHtml::submitButton(
            $text,
            array_merge([
                "class" => "button large",
                "name" => "save",
                "id" => "et_save"
            ], $htmlOption)
        );
    }

    public static function addButton($text = "Add", $htmlOption = [])
    {
        return \CHtml::submitButton(
            $text,
            array_merge([
                "class" => "button large",
                "name" => "add",
                "id" => "et_add"
            ], $htmlOption)
        );
    }

    public static function cancelButton($text = "Cancel", $htmlOption = [])
    {
        return \CHtml::submitButton(
            $text,
            array_merge([
                "class" => "warning button large",
                "name" => "cancel",
                "id" => "et_cancel"
            ], $htmlOption)
        );
    }

    public static function linkButton($text = "Add", $url = '#', $htmlOption = [])
    {
        return \CHtml::link(
            $text,
            $url,
            array_merge([
                "class" => "button large green hint",
            ], $htmlOption)
        );
    }
}
