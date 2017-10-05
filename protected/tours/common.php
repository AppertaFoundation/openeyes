<?php
return array(
    array(
        'name' => 'OpenEyes Welcome',
        'id' => 'openeyes-welcome',
        'url_pattern' => '/^\/{0,1}$/',
        'steps' => array(
            array(
                'orphan' => true,
                'title' => 'Welcome to OpenEyes',
                'content' => 'OpenEyes is an Open Source Ophthalmic EPR. It is going to rock your world!'
            ),
            array(
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