Patient Ticketing Module
========================

This module is intended to provide a generic ticketing mechanism to track patients through multiple pathways. As a standalone module, it will require other modules to integrate with it to create patient tickets. In its first iteration it is intended to support Virtual Clinics, but it is being implemented in a way to be suitable for clinic tracking, application processing and (hopefully) any other pathway that might occur for a patient.

Setup
=====

1. Place the module code in the usual modules directory (protected/modules)
1. Add the module to the yii local config:

        'modules' => array(
            ...
            'PatientTicketing' => array('class' => '\OEModule\PatientTicketing\PatientTicketingModule'),
            ...
        )

1. In user admin, give the users you want to have access to Patient Ticketing the Patient Ticket permission.
1. Use the Patient Ticketing admin to setup one or more queue.

Raising Tickets
===============

At the moment, tickets can only be raised in supporting modules:

1. OphCiExamination - The Clinic Outcome element

Assignment Widgets
==================

Currently, the configuration of fields for ticket assignment to queues is json blob of structured data. Basic field types are choices (drop down) and text. However widgets provide the opportunity to define more sophisticated behaviour in the queue assignment form. The initial example of this is TicketAssignOutcome, a widget that provides outcomes, and dynamically a follow up and site selection. A basic configuration for this:

    {"id":"glreview", "type":"widget", "widget_name": "TicketAssignOutcome"}


