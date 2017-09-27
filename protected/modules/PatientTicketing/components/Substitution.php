<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\PatientTicketing\components;

/**
 * Class Substitution.
 *
 * @TODO: this is taken from correspondence and I think we should make it a part of core, given that it's solely based on
 * PatientShortcode which is a core component.
 */
class Substitution
{
    /**
     * @param $text
     * @param $patient
     *
     * @return mixed
     *
     * @throws Exception
     */
    public static function replace($text, $patient)
    {
        preg_match_all('/\[([a-z]{3})\]/is', $text, $m);

        foreach ($m[1] as $el) {
            $count = \PatientShortcode::model()->count('code=?', array(strtolower($el)));

            if ($count == 1) {
                if ($code = \PatientShortcode::model()->find('code=?', array(strtolower($el)))) {
                    $text = $code->replaceText($text, $patient, (boolean) preg_match('/^[A-Z]/', $el));
                }
            } elseif ($count > 1) {
                throw new \Exception("Multiple shortcode definitions for $el");
            }
        }

        return $text;
    }
}
