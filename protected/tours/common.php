<?php
return array(
    array(
        // title displayed in the popup Help menu for tours
        'name' => 'OpenEyes Welcome',
        // the id is a unique name for identifying the tour and tracking state for users
        // a naming convention should be adhered to ensure unique-ness.
        'id' => 'openeyes-welcome',
        // this regex matches on the URL to determine what pages a tour should be part of
        'url_pattern' => '/^\/{0,1}$/',
        // tours are sorted by position, with the lowest being the first in the list
        'position' => 10,
        // the auto flag means that if the user has not seen this tour before, it will
        // automatically start when they reach the appropriate point in the application
        'auto' => true,
        'steps' => array(
            // each step is used by bootstrap-tour for the actual tour of the site.
            // see http://bootstraptour.com/api/ for details
            array(
                // the orphan flag puts the step in the centre of the page, unattached.
                'orphan' => true,
                'title' => 'Welcome to OpenEyes',
                'content' => 'This looks like the first time you\'ve logged in, so here is a quick tour to get you oriented<br/>Click Next >> to continue...',
            ),
            array(
                'element' => '.ui-dialog .ui-dialog-content',
                'title' => 'Set your working context',
                'content' => 'First things first, you need to say which site you\'re working at. Don\'t worry about \'Firms\' for now',
                'showParent' => 'true',
            ),
            array(
                'element' => '.oe-find-patient:first',
                'title' => 'Finding patients',
                'content' => 'You can open a patient record from the search box by entering a hospital number, NHS number or the patient\'s name',
                'showParent' => 'true'
            ),
            array(
                'element' => "[id$='inbox-container']",
                'title' => 'Messages',
                'content' => 'If a colleague sends you a message about a patient, it will show here',
                'placement' => 'left',
            ),
            array(
                // the element attribute is the selector used for locating the section of the page to highlight
                'element' => "[id$='-automatic-worklists-container'] > .js-toggle-body > .row:first-of-type",
                'title' => 'Clinic lists',
                'content' => 'Here you will see all booked patient appointments in your clinic(s). Arrival information is updated live from your PAS<br/><br/>Clicking on a patient name will take you directly to the patient record',
                // placement options can be one of 'top', 'bottom', 'left', 'right', 'auto'.
                'placement' => 'left',

            ),
            array(
                'element' => '.oe-user-info',
                'title' => 'Current context and profile',
                'content' => 'Use the \'profile\' link to edit your profile.<br/><br/>This panel also reminds you who you\'re logged in as, what site you are working at and your current working context.',
            ),
            array(
                'element' => '.oe-user-home',
                'title' => 'Home Button',
                'content' => 'Clicking this button at any time will return you to this screen (which is known as your "Home Screen")',
            ),
            array(
                'element' => '.oe-user-navigation',
                'title' => 'Menu',
                'content' => 'This is the menu, depending on your permissions type this will give you access to various extra functions of the system',
            ),
            array(
                'element' => '.oe-user-logout',
                'title' => 'Logout',
                'content' => 'This button will log you out of OpenEyes. Use it when you\'ve finished your work',
                'placement' => 'left',
            )
        )
    ),
    array(
        'name' => 'Uservoice Intro',
        'id' => 'openeyes-uservoice',
        'url_pattern' => '/^\/{0,1}$/',
        'position' => 1,
        'auto' => true,
        'steps' => array(
            array(
                'element' => '.uv-bottom-right',
                'title' => 'Got an idea?',
                'content' => 'OpenEyes is a collaborative project. If you have an idea for an improvement, we\'d love to hear it. Just click on this bubble and submit your idea straight to our community forum, then we\'ll do our best to include it in a future release',
                'placement' => 'left',
            )
        )
    ),
    array(
        'name' => 'Examination Overview',
        'id' => 'examination-overview',
        'auto' => true,
        'url_pattern' => '|^/OphCiExamination/Default/create|i',
        'steps' => array(
            array(
                'orphan' => true,
                'title' => 'Examination event',
                'content' => 'This is the Examination event. It allows you to record many clinical details about your patient, including; Past medical/Ophthalmic history, observations, diagnoses and clinical management.<br><br>This is the biggest event in OpenEyes, so let\'s take a look around to familiarise ourselves with it',
            ),
            array(
                'element' => '#event-content',
                'title' => 'Elements',
                'placement' => 'top',
                'content' => 'The form elements within the event window allow you to record the various clinical detail required at this stage.'
            ),
            array(
                'element' => '#patient-sidebar-elements',
                'title' => 'Adding Elements',
                'placement' => 'right',
                'content' => 'You can use the element sidebar to select additional elements to add to the event, or to navigate to elements already in the form.'
            ),
            array(
                'element' => '#search_bars_and_options',
                'title' => 'Find clinical concepts',
                'placement' => 'bottom',
                'content' => 'Or you can use these search fields to look directly for clinical concepts you wish to record on your patient.'
            ),
            array(
                'element' => '#event.title',
                'title' => 'Multi-step workflows',
                'content' => 'Exam events may be completed in a number of \'steps\' by different people (with each person completing different sections).<br>When you are in a multi-step workflow, the current step name is shown here in brackets' ,
                'placement' => 'bottom',
                'showParent' => true,
            ),
        )
    )
);
