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
        'position' => 20,
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
                // the element attribute is the selector used for locating the section of the page to highlight
                'element' => "input#query",
                'title' => 'Finding patients',
                'content' => 'You can open a patient record from the search box by entering a hospital number, ' . PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(Yii::app()->params['display_primary_number_usage_code'], $this->selectedInstitutionId, $this->selectedSiteId) . ' number or the patient\'s name',
                'showParent' => 'true',
                //backdropElement can be used to highlight a different element to the one selected by 'element'
                'backdropElement' => '.oe-find-patient:first',
            ),
            array(
                'element' => "[id$='inbox-container']",
                'title' => 'Messages',
                'content' => 'If a colleague sends you a message about a patient, it will show here',
                'placement' => 'bottom',
            ),
            array(
                'element' => "[id$='-automatic-worklists-container'] > .js-toggle-body > .row:first-of-type",
                'title' => 'Clinic lists',
                'content' => 'Here you will see all booked patient appointments in your clinic(s). Arrival information is updated live from your PAS<br/><br/>Clicking on a patient name will take you directly to the patient record',
                // placement options can be one of 'top', 'bottom', 'left', 'right', 'auto'.
                'placement' => 'left',
                'backdropElement' => "[id$='-automatic-worklists-container'] > .js-toggle-body > .row:first-of-type, [id$='-automatic-worklists-container'] > .js-toggle-body > h1:first-of-type"
            ),
            array(
                'element' => '#site-context-box',
                'title' => 'Set your working context',
                'content' => 'Use this box to change which clinic list(s) are displayed, depending on where you are (site) and which working \'context\' you are interested in',
                //'showParent' => 'true',
            ),
            // array(
            //     'element' => '.oe-user-info',
            //     'title' => 'Current context and profile',
            //     'content' => 'Use the \'profile\' link to edit your profile.<br/><br/>This panel also reminds you who you\'re logged in as, what site you are working at and your current working context.',
            //     'backdropElement' => "div.panel.user",
            // ),
            array(
                'element' => '.oe-user-home',
                'title' => 'Home Button',
                'content' => 'Clicking this button at any time will return you to this screen (which is known as your "Home Screen")',
                'backdropElement' => "div.panel.user",
            ),
            array(
                'element' => '.oe-user-navigation',
                'title' => 'Menu',
                'content' => 'This is the menu, depending on your permissions type this will give you access to various extra functions of the system',
                'backdropElement' => "div.panel.user",
                'placement' => 'left',
            ),
            array(
                'element' => '.oe-user-logout',
                'title' => 'Logout',
                'content' => 'This button will log you out of OpenEyes. Use it when you\'ve finished your work',
                'placement' => 'left',
                'backdropElement' => "div.panel.user",
                'placement' => 'left',
            ),
            array(
                'element' => '.uv-bottom-right',
                'title' => 'Got an idea?',
                'content' => 'OpenEyes is a collaborative project. If you have an idea for an improvement, we\'d love to hear it. Just click on this bubble and submit your idea straight to our community forum, then we\'ll do our best to include it in a future release',
                'placement' => 'left',
            )
        )
    ),
    array(
        'name' => 'Patient Header',
        'id' => 'patient-header-tour',
        'auto' => true,
        #'url_pattern' => '~^/Oph.*/default/view/~i',
        'url_pattern' => '~^(/patient/episode/)|(/Oph.*/default/view/)~i',
        'position' => 33,
        'steps' => array(
            // array(
            //     'element' => '.patient.panel',
            //     'title' => 'Patient Panel',
            //     'content' => 'This panel shows you the current active patient. It will be shown at the top of all screens when you are in a patient record.',
            //     'placement' => 'bottom',
            // ),
            array(
                'element' => 'button.toggle-patient-summary-popup.icon-alert-quicklook',
                'title' => 'Summary pop-up',
                'content' => 'Hover the mouse over this icon and a summary of the patient\'s current state will be shown (diagnoses, allergies, medications, history, etc.)<br><br>You can use this to quickly get an overview of the patient. It can be accessed from any screen.<br><br><b>Tip:</b> Clicking the icon will lock the panel in place.',
                'backdropPadding' => 20,
            ),
        )
    ),
    array(
        'name' => 'Adding Events',
        'id' => 'blank-patient-intro',
        'auto' => true,
        'url_pattern' => '~^/patient/episodes/~i',
        'position' => 50,
        'steps' => array(
            array(
                'element' => 'div.oe-no-episodes > .add-event',
                'title' => 'Creating first event',
                'content' => 'This patient is new and has no events recorded.<br><br>There are a lot of different types of event in OpenEyes. E.g, Examinations, Operation Notes, Consent Forms, etc.<br><br>To start recording the first event for this patient, click this button',
                'backdropElement' => ".oe-no-episodes"
            ),
        )
    ),
    array(
        'name' => 'Events Intro',
        'id' => 'events-intro',
        'auto' => true,
        # Show when viewing an episode summary view OR when viewing an event (these are the 2 situtaions in which the event list is visible)
        'url_pattern' => '~^/patient/episode/~i',
        'position' => 22,
        'steps' => array(
            array(
                'element' => 'ol.events',
                'title' => 'Event list',
                'content' => "Each patient encounter in OpenEyes is recorded in an event.<br/><br/>As new events are recorded, they will be shown in this list. You can click on any event to view it<br/><br/>There are lots of different types of event (e.g, Examinations, Operation Notes, Consent Forms and more). Each event type has a different icon to make it easy to identify",
            ),
            array(
                'element' => 'div.controls',
                'title' => 'Sorting and Grouping Events',
                'content' => 'You can use these controls to order and group the list of events, to make it easier to find what you need. <br><br>The arrows can be used to sort events by newest or oldest first<br><br>The dropdown can be used to group events by type, subspecialty or date',
            ),
            array(
                'element' => 'ol.subspecialties',
                'title' => 'Subspecialty Summaries',
                'content' => "To see a subspecialty-specific summary screen for this patient, click on the subspecialty name here.<br><br>There will be a different summary for each subspecialty that the patient is active in.",
            ),
            array(
                'element' => "div.oe-sidebar-top-buttons > .add-event",
                'title' => 'Add a new Event',
                'content' => 'To start recording a new event, use this button.<br>There are a lot of different types of event. E.g, Examinations, Operation Notes, Consent Forms, etc.',
            ),
        ),
    ),
    // Disabled - requires JS to force open the create dialog
    // array(
    //     'name' => 'Create Event Intro',
    //     'id' => 'create-event-intro',
    //     'auto' => true,
    //     'url_pattern' => '~^/patient/episode[s]?/~i',
    //     'position' => 90,
    //     'steps' => array(
    //         array(
    //             'element' => 'td.step-subspecialties',
    //             'backdropElement' => ".oe-create-event-popup",
    //             'title' => 'Choose subspecialty',
    //             'content' => "First, you must select which subspecialty the new event belongs to.<br>Please select at least one subspecialty before clicking Next>><br><br><b>Tip:</b>When adding a new subspecialty, remember to click the blue [+] icon.",
    //             'delay' => 600,
    //             'placement' => 'top',
    //         ),
    //         array(
    //             'element' => 'td.step-context',
    //             'backdropElement' => ".oe-create-event-popup",
    //             'title' => 'Choose Context',
    //             'content' => "Now, pick the context you are currently working in - This will affect which workflow/pathway is used and what default options are selected<br><br>(This is particularly important when creating an Examaination Event)",
    //             'placement' => 'top',
    //         ),
    //         array(
    //             'element' => 'td.step-event-types',
    //             'backdropElement' => ".oe-create-event-popup",
    //             'title' => 'Choose Event Type',
    //             'content' => "Lastly, choose which type of event you want to create from the list.<br><br><b>Note:</b> If an event type is greyed-out, it means you do not have permission to use it",
    //             'placement' => 'top',
    //         ),
    //     ),
    // ),

    ######################################################################
    # Examination Event
    ######################################################################

    array(
        'name' => 'Examination Overview',
        'id' => 'examination-overview',
        'auto' => true,
        'url_pattern' => '|^/OphCiExamination/Default/create|i',
        'steps' => array(
            array(
                'orphan' => true,
                'title' => 'Introduction to the Examination event',
                'content' => 'This is the Examination event. It allows you to record many clinical details about your patient, including; Past medical/Ophthalmic history, observations, diagnoses and clinical management.<br><br>This is the biggest event in OpenEyes, so let\'s take a look around to familiarise ourselves with it',
            ),
            array(
                'element' => "input#search_bar_right",
                'backdropElement' => "div#search_bars_and_options",
                'title' => 'Find anything',
                'placement' => 'bottom',
                'content' => "If you ever have trouble finding what you're looking for, you can use these handy search boxes.<br><br>These will take you directly to the place on the page that you need. And for Eyedraws, it will even add the item or you!<br><br><b>Tip:</b> This is a real time-saver",
                'backdropPadding' => 8,
            ),
            array(
                'element' => '#event-content',
                'placement' => 'top',
                'content' => "This is where you record data. Each section focuses on different areas of information.<br><br>The sections shown are determined by the 'Context' that you selected when creating the event.",
            ),
            array(
                'element' => '#patient-sidebar-elements',
                'title' => 'Adding and navigating sections',
                'placement' => 'right',
                'content' => "You can use this side-bar to add more sections to the page. The white sections are ones that are already open. Clicking on one of these will navigate you directly to that section of the page<br><br>As you can see, there are lots of different things you can record in an Examination. Don't forget you can always use the search boxes at the top if you have trouble finding what you need",
            ),
            array(
                'element' => '.js-remove-element:eq(2)',
                'title' => 'Removing sections',
                'placement' => 'top',
                'content' => "If a section is open then it <b>must</b> be filled in. If you don't want to fill it in then you can use this close button in the section header to remove it<br><br><b>Hint:</b> You can always add the section back again using the side-bar.",
                'backdropElement' => '.element-header:eq(2)',
            ),
            array(
                'element' => 'h2.event-title',
                'title' => 'Multi-step workflows',
                'content' => 'Exam events may be completed in a number of \'steps\' by different people (with each person completing different sections).<br>When you are in a multi-step workflow, the current step name is shown here in brackets' ,
                'placement' => 'bottom',
            ),
        ),
    ),


######################################################################
# Biometry Event
######################################################################

    array(
        'name' => 'Select Biometry Report',
        'id' => 'biometry-select-report',
        'auto' => true,
        'url_pattern' => '|^/OphInBiometry/Default/create|i',
        'position' => 5,
        'steps' => array(
            array(
                'element' => 'form#biometry-event-select div.element-fields',
                'title' => 'Choose an available report',
                'content' => "This is a list of all the reports available for this patient from your biometry device(es). Please select which of these reports you would like to use and click the Continue button",
                'placement' => 'top',
            ),
        ),
    ),
    array(
        'name' => 'Choose a lens',
        'id' => 'biometry-point-to-lens-button',
        'auto' => true,
        'url_pattern' => '|^/OphInBiometry/default/view/|i',
        'position' => 90,
        'steps' => array(
            array(
                'element' => "a.button:contains('Choose Lens')",
                'title' => 'TIP:',
                'content' => "When you want to select a lens for surgery, Click this button",
                'placement' => 'auto',
            ),
        ),
    ),
    array(
        'name' => 'Making your lens choice',
        'id' => 'biometry-lens-choice',
        'auto' => false,
        'url_pattern' => '|^/OphInBiometry/default/update/|i',
        'position' => 10,
        'steps' => array(
            array(
                'element' => "[id^='Element_OphInBiometry_Selection_lens_id']:first",
                'title' => 'Choose lens',
                'content' => "First, select your preferred lens, then your preferred formula from these boxes. You will then be shown a choice of target refractions",
                'placement' => 'auto',
                'backdropElement' => "[id$='-eye-selection']:first",
            ),
            array(
                'element' => "[id$='-eye-selection']:first table:visible",
                'title' => 'Choose Power',
                'content' => "Select the row in the table that most closely matches your desired post-operative refraction.<br><br><b>The closest option to your previously indicated target is in bold</b><br><br>These choices ae calculated by your device. If you calculate more target refractions on your device, they will be added to this list",
                'placement' => 'bottom',
                'backdropElement' => "[id$='-eye-selection']:first",
            ),
            array(
                'element' => "section.Element_OphInBiometry_Measurement.element:first",
                'content' => "Latest known refration and Visual Acuity is displayed here to help you with your choice, along with all the eye measurements",
                'placement' => 'auto',

            ),
            array(
                'element' => "button#et_save",
                'content' => "Once you've finished making your choice(s), click save to finalise. Your choice will then be pulled automatically into the Operation Note. It will also show on the electronic Theatre Whireboard.",
                'placement' => 'auto',

            ),
        ),
    ),


    ######################################################################
    # Operation Booking Event
    ######################################################################

    array(
        'name' => 'Theatre whiteboard',
        'id' => 'op-booking-whiteboard-button',
        'auto' => true,
        'url_pattern' => '|^/OphTrOperationbooking/default/view/|i',
        'position' => 6,
        'steps' => array(
            array(
                'element' => "a:contains('Whiteboard')",
                'title' => 'Whiteboard view',
                'content' => "Clicking this button will bring up the theatre whiteboard view.<br/><br/>You can use this to record important comments / reminders to be shown during the operation.<br/><br/>The whiteboard will update when you make a lens selection or if any risks/allergies are recorded against the patient",
                'placement' => 'auto'
            ),
        ),
    ),

);
