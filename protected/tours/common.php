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
                'content' => 'OpenEyes is an Open Source Ophthalmic EPR. It is going to rock your world!'
            ),
            array(
                // the element attribute is the selector used for locating the section of the page to highlight
                'element' => '.large-6.medium-7.column',
                'title' => 'User',
                'content' => 'All the details regarding your current context can be seen here.',
                'placement' => 'bottom',
                'backdropContainer' => 'header'
            ),
            array(
                'element' => '.oe-find-patient:first',
                'title' => 'Patient',
                'content' => 'The search box allows you to find a patient by name, nhs number or hospital number.',
                'showParent' => 'true'
            )
        )
    ),
    array(
        'name' => 'Version 2',
        'id' => 'openeyes-v2',
        'url_pattern' => '/^\/{0,1}$/',
        'position' => 1,
        'auto' => true,
        'steps' => array(
            array(
                'orphan' => true,
                'title' => 'Welcome to Version 2',
                'content' => 'Version 2 of OpenEyes introduces several key features:<ul><li>Single Episode Model</li><li>Eyedraw object persistence</li></ul>'
            ),
            array(
                'orphan' => true,
                'title' => 'Welcome to Version 2',
                'content' => 'Tours will be shown in various key locations of the application to highlight these changes.'
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
                'title' => 'Creating an examination',
                'content' => 'The examination event allows you to record many clinical details about your patient.'
            ),
            array(
                'element' => '.oe-user-panel .oe-user-profile-firm',
                'title' => 'Logged in context',
                'content' => 'The context with which you have begun the examination event will affect the elements which are open.',
                'placement' => 'bottom',
                'showParent' => true
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
        )
    )
);