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

// | id | address1 | address2       | city      | postcode | county | country_id | email                   |
// +----+----------+----------------+-----------+----------+--------+------------+-------------------------+
// |  1 | flat 1   | bleakley creek | flitchley | ec1v 0dx | london |          1 | bleakley1@bleakley1.com |
// |  2 | flat 2   | bleakley creek | flitchley | ec1v 0dx | london |          1 | bleakley2@bleakley2.com |
// |  3 | flat 3   | bleakley creek | flitchley | ec1v 0dx | london |          1 | bleakley3@bleakley3.com |

return array(
    'address1' => array(
        'address1' => 'flat 1',
        'address2' => 'bleakley creek',
        'city' => 'flitchley',
        'postcode' => 'ec1v 0dx',
        'county' => 'london',
        'country_id' => 1,
        'contact_id' => 1,
    ),
    'address2' => array(
        'address1' => 'flat 2',
        'address2' => 'bleakley creek',
        'city' => 'flitchley',
        'postcode' => 'ec1v 0dx',
        'county' => 'london',
        'country_id' => 1,
        'contact_id' => 2,
    ),
    'address3' => array(
        'address1' => 'flat 3',
        'address2' => 'bleakley creek',
        'city' => 'flitchley',
        'postcode' => 'ec1v 0dx',
        'county' => 'london',
        'country_id' => 1,
        'contact_id' => 3,
    ),
);
