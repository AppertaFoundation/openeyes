Messaging
=========

Event messaging module to allow notes to be made on patient records. Provides a dashboard view of messages that have been created for a user.

Configuration
=============

Add the module to the application configuration:

    'OphCoMessaging' => array('class' => '\OEModule\OphCoMessaging\OphCoMessagingModule')


In the module config is the details for the dashboard integration


    'params' => array(
        'dashboard_items' => array(
           array(
                'module' => 'OphCoMessaging',
				
                // default action is the 'renderDashboard' if 'actions' array is  not set
                'actions' => array(
                    'getInboxMessages',
                    'getSentMessages',
                 )
            ),
        )
    )

Notes
=====

1. The comments on messages have been defined in the database to support multiple comments on a single message. However the UI has been deliberately setup to only support single comments at this point.
1. An effort has been made to integrate the controller flow with the standard model as much as possible. This has led to a little bit of hoop jumping in places.
