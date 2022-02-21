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
    'import' => array(
        'application.modules.OphTrOperationbooking.components.OphTrOperationbookingObserver',
    ),
    'components' => array(
        'event' => array(
            'observers' => array(
                'firm_changed' => array(
                    'ophtroperationbooking_resetsearch' => array(
                        'class' => 'OphTrOperationbookingObserver',
                        'method' => 'resetSearch',
                    ),
                ),
            ),
        ),
    ),
    'params' => array(
        'menu_bar_items' => array(
            'theatre_diaries' => array(
                'title' => 'Theatre Diaries',
                'uri' => 'OphTrOperationbooking/theatreDiary/index',
                'position' => 10,
                'requires_setting' => array('setting_key'=>'disable_theatre_diary', 'required_value'=>'off')
            ),
            'partial_bookings' => array(
                'title' => 'Partial bookings waiting list',
                'uri' => 'OphTrOperationbooking/waitingList/index',
                'position' => 20,
                'restricted' => array('Schedule operation' , 'Super schedule operation'),
            ),
        ),
        'future_scheduling_limit' => '3 months',
        'admin_menu' => array(
            'OphTrOperationbooking' => [
                'Sequences' => array('uri'=>'/OphTrOperationbooking/admin/viewSequences', 'requires_setting' => array('setting_key'=>'disable_theatre_diary', 'required_value'=>'off')),
                'Sessions' => array('uri'=>'/OphTrOperationbooking/admin/viewSessions', 'requires_setting' => array('setting_key'=>'disable_theatre_diary', 'required_value'=>'off')),
                'Wards' => '/OphTrOperationbooking/admin/viewWards',
                'Theatres' => '/OphTrOperationbooking/admin/viewTheatres',
                'Operation priorities' => '/OphTrOperationbooking/admin/operationPriorities',
                'Scheduling options' => '/OphTrOperationbooking/admin/scheduleOptions',
                'EROD rules' => '/OphTrOperationbooking/admin/viewERODRules',
                'Letter contact rules' => '/OphTrOperationbooking/admin/viewLetterContactRules',
                'Letter warning rules' => '/OphTrOperationbooking/admin/viewLetterWarningRules',
                'Operation name rules' => '/OphTrOperationbooking/admin/viewOperationNameRules',
                'Waiting list contact rules' => '/OphTrOperationbooking/admin/viewWaitingListContactRules',
                'Patient unavailable reasons' => '/OphTrOperationbooking/admin/viewPatientUnavailableReasons',
                'Session unavailable reasons' => array('uri'=>'/OphTrOperationbooking/admin/viewSessionUnavailableReasons', 'requires_setting' => array('setting_key'=>'disable_theatre_diary', 'required_value'=>'off')),
                'Whiteboard' => '/OphTrOperationbooking/oeadmin/WhiteboardSettings/settings',
            ]
        ),
        // Default anaesthetic settings
        //'ophtroperationbooking_default_anaesthetic_child' => 'GA',
        //'ophtroperationbooking_default_anaesthetic' => 'GA',
        // How many weeks from DTA should EROD be calculated
        //'erod_lead_time_weeks' => 3,
        // How many days ahead of the day an operation is being scheduled should EROD be calculated
        //'erod_lead_current_date_days' => 2,
        // number of weeks from decision date that is the RTT limit
        //'ophtroperationboooking_rtt_limit' => 6,
        // whether referrals can be assigned to operation bookings or not (turn off if you don't have referrals imported
        // or set on the patient record.
        //'ophtroperationbooking_referral_link' => true,
        // boolean to require a referral on an operation booking for scheduling or not
        //'ophtroperationbooking_schedulerequiresreferral' => true

        'whiteboard' => array(
            // whiteboard will be refresh-able after operation booking is completed
            // overrides admin > Opbooking > whiteboard settings
            //'refresh_after_opbooking_completed' => 24, //hours or false
        ),
        'reports' => array(
            'Effective use of resources (EUR)' => '/OphTrOperationbooking/report/eur'
        ),
    ),
);
