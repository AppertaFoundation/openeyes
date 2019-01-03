OETrial Module
========================

This module is intended to provide a Trial management tool for research organisations.

Setup
-----

1. Place the module code in the usual modules directory (protected/modules)
1. Add the module to the yii local config:

        'modules' => array(
            ...
            'OETrial',
            ...
        )

1. OETrial leverages OECaseSearch to add patients to a Trial, so the OECaseSearch module must also be loaded.

Trial Management
----------------

The Trial management screen can be accessed through the Trials top level menu item. 
Users can only see this link if they have the "Create Trial" or "View Trial" privileges.
Only users with the "Create Trial" privilege can start new Trials.
