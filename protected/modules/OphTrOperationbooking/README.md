# OphTrOperationbooking

Operation booking event type.

This module was developed to replace core functionality for operation booking, and was part of the 1.3 release.

Migration From Core (1.2 or lower)
----------------------------------

The OpenEyes team may be able to help if you have any mission critical bookings from core that need to be migrated, but if this can be avoided you are advised to reset your database and set up a clean system with the modularisation of booking in place.

## Known Technical Debt

### Diagnoses

The `Element_OphTrOperationbooking_Diagnosis` class contains code to attach entered diagnoses to the patient record in both the `Episode` and as a `SecondaryDiagnosis`.

This is legacy functionality, and the UI view now only provides diagnoses for selection when they have already been set on the patient through examination.