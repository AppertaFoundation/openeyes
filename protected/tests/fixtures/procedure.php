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

return array(
    'procedure1' => array(
        'term' => 'Test procedure 1',
        'short_format' => 'TP1',
        'default_duration' => 1,
        'snomed_code' => 111111,
        'snomed_term' => 'TEst',
        'aliases' => 'T',
        'unbooked' => 0,
        'active' => 1,
        'low_complexity_criteria' => '
        <p>Biometry</p>
        <ul>
        <li>Toric Lens</li>
        <li>AL &lt;21.0mm or AL &gt;29.0mm</li>
        <li>ACD &lt;2.5mm<br /><br />Patient Factor</li>
        <li>White / Brunescent / No fundal view</li>
        <li>Small pupil (&lt;6mm after trop &amp; phenyl)</li>
        <li>Only eye (other eye potential &lt;6/60)</li>
        <li>Cognitive Impairment</li>
        <li>GA</li>
        <li>Difficult positioning / Tremor</li>
        </ul>
        ',
    ),
    'procedure2' => array(
        'term' => 'Test procedure 1',
        'short_format' => 'TP1',
        'default_duration' => 1,
        'snomed_code' => 111111,
        'snomed_term' => 'TEst',
        'aliases' => 'T',
        'unbooked' => 0,
        'active' => 1,
        'low_complexity_criteria' => '
        <p>Biometry</p>
        <ul>
        <li>Toric Lens</li>
        <li>AL &lt;21.0mm or AL &gt;29.0mm</li>
        <li>ACD &lt;2.5mm<br /><br />Patient Factor</li>
        <li>White / Brunescent / No fundal view</li>
        <li>Small pupil (&lt;6mm after trop &amp; phenyl)</li>
        <li>Only eye (other eye potential &lt;6/60)</li>
        <li>Cognitive Impairment</li>
        <li>GA</li>
        <li>Difficult positioning / Tremor</li>
        </ul>
        ',
    ),
);
