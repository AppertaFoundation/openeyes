<?php
return array(
    array(
        'name' => 'OpenEyes Welcome',
        'id' => 'openeyes-welcome',
        'url_pattern' => '^/{0,1}$',
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
    )
);