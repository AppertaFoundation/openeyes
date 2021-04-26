<?php
/**
 * OpenEyes.
 *
 *
 * Copyright OpenEyes Foundation, 2020
 *
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class OELinebreakReplacer
{
    public static function replace($text)
    {
        $text = preg_replace('/(\n[\n]+|\r\n[\r\n]+|\r[\r]+)/', '<i class="oe-i d-slash small pad"></i>', trim($text));
        $text = preg_replace('/(\n|\r\n|\r)/', '<i class="oe-i line-break"></i>', $text);
        return $text;
    }

    public static function plainTextReplace($text)
    {
        return preg_replace('/[\n\r]+/', " ", trim($text));
    }
}
