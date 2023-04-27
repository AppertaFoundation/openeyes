<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace services;

class DateTime extends \DateTime implements FhirCompatible
{
    public static function fromFhir($value)
    {
        return new self($value);
    }

    public function toFhir()
    {
        return $this->format(DATE_RFC3339);
    }

    public function relative()
    {
        $diff = $this->diff(new DateTime());

        if ($diff->y > 0) {
            return $diff->y . ($diff->y === 1 ? ' year ago' : ' years ago');
        } else if ($diff->m > 0) {
            return $diff->m . ($diff->m === 1 ? ' month ago' : ' months ago');
        } else if ($diff->d > 0) {
            return $diff->d . ($diff->d === 1 ? ' day ago' : ' days ago');
        } else if ($diff->h > 0) {
            return $diff->h . ($diff->h === 1 ? ' hour ago' : ' hours ago');
        } else if ($diff->i > 0) {
            return $diff->i . ($diff->i === 1 ? ' minute ago' : ' minutes ago');
        } else {
            return $diff->s . ($diff->s === 1 ? ' second ago' : ' seconds ago');
        }
    }
}
