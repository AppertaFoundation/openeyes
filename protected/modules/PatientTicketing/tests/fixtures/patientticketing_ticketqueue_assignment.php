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

return array(
    'ass1' => array(
        'ticket_id' => 1,
        'queue_id' => 1,
        'assignment_date' => '2014-09-04',
        'assignment_user_id' => 1,
        'assignment_firm_id' => 1,
    ),
    'ass2' => array(
        'ticket_id' => 2,
        'queue_id' => 12,
        'assignment_date' => '2014-09-01',
        'assignment_user_id' => 1,
        'assignment_firm_id' => 1,
    ),
    'ass3' => array(
        'ticket_id' => 3,
        'queue_id' => 12,
        'report' => 'test report',
        'assignment_date' => '2014-09-05',
        'assignment_user_id' => 1,
        'assignment_firm_id' => 1,
    ),
    'ass4' => array(
        'ticket_id' => 3,
        'queue_id' => 13,
        'report' => 'updated test report',
        'assignment_date' => '2014-09-07',
        'assignment_user_id' => 1,
        'assignment_firm_id' => 1,
    ),
    'ass5' => array(
        'ticket_id' => 4,
        'queue_id' => 12,
        'report' => 'test report',
        'assignment_date' => '2014-09-01',
        'assignment_user_id' => 1,
        'assignment_firm_id' => 1,
    ),
    'ass6' => array(
        'ticket_id' => 4,
        'queue_id' => 13,
        'assignment_date' => '2014-09-02',
        'assignment_user_id' => 1,
        'assignment_firm_id' => 1,
    ),
    'ass7' => array(
        'ticket_id' => 5,
        'queue_id' => 12,
        'report' => 'Follow up in 2 weeks at Boots Opticians',
        'details' => '[{"id":"glreview","widget_name":"TicketAssignOutcome","value":{"outcome":"2","followup_quantity":"2","followup_period":"weeks","site":"Boots Opticians"}}]',
        'assignment_date' => '2014-09-01',
        'assignment_user_id' => 1,
        'assignment_firm_id' => 1,
    ),
);
