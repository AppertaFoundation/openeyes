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

/**
 * Class EyedrawElementBehavior
 *
 * A simple behavior class to provide standardised reporting behavior.
 *
 */
class EyedrawElementBehavior extends CModelBehavior
{
    /**
     * @return string
     */
    public function getLetter_string()
    {
        $result = property_exists($this->owner, 'letter_string_prefix') ?
            $this->owner->letter_string_prefix : '';

        foreach (array('right', 'left') as $side) {
            if (!$this->owner->{'has' . ucfirst($side)}()) {
                continue;
            }
            $items = array();
            foreach (array('_ed_report', '_description') as $attr) {
                if ($this->owner->{$side . $attr}) {
                    $items[] = $this->owner->{$side . $attr};
                }
            }
            $result .= $side . ': ' . implode(' ', $items) . "\n";
        }
        return $result;
    }

}
