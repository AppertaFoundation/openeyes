@editdelete @regression
Feature: These tests set up Events, Edit and Delete them.

  Scenario: Route 1A: Login and create a Anaesthetic Satisfaction Audit Regression: Site 2 Kings, Firm 3 Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Satisfaction"

    Then I select an Anaesthetist "no"
    And I select Satisfaction levels of Pain "2" Nausea "3"

    And I tick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "3" Oxygen Saturation "3" Systolic Blood Pressure "4"
    And I select Vital Signs of Body Temperature "5" and Heart Rate "2" Conscious Level AVPU "2"

    Then I enter Comments "This test is for Site 2 Kings, Firm 3 Anderson Glaucoma"

    And I select the Yes option for Ready to Discharge

    Then I Save the Event

  Scenario: Route 1B: Edit previously created ASA from Route1A

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    And I edit the Last Event

    Then I select an Anaesthetist "non"
    And I select Satisfaction levels of Pain "4" Nausea "1"

    And I untick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "4" Oxygen Saturation "1" Systolic Blood Pressure "5"
    And I select Vital Signs of Body Temperature "1" and Heart Rate "5" Conscious Level AVPU "5"

    Then I enter Comments "Route 1 ASA Edit and Save Test"

    And I select the No option for Ready to Discharge

    Then I Save the Event

  Scenario: Route 1C: Delete previously created/edited ASA from Route1A/1B

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    And I delete the Last Event

  Scenario: Route 2A: Login and create a new Consent Form

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Consent"
    Then I select Unbooked Procedures
    Then I select Add Consent Form
    And I choose Type "1"

    Then I choose Procedure eye of "Both"
    And I choose an Anaesthetic type of LA
    And I add a common procedure of "127"

    Then I choose Permissions for images No

    Then I save the Consent Form and confirm it has been created successfully

  Scenario: Route 2B: Edit previously created Consent from Route2A

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    And I edit the Last Event

    And I choose Type "2"

    Then I choose Procedure eye of "Right"
    And I choose an Anaesthetic type of LAC

    Then I choose Permissions for images No

    Then I save the Consent Form

  Scenario: Route 2C: Delete previously created/edited Consent From from Route2A/2B

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    And I delete the Last Event

Scenario: Route 3A: Login and create a Phasing Event

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "1009465 "

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Phasing"

    Then I choose a right eye Intraocular Pressure Instrument  of "1"

    And I choose right eye Dilation of Yes

    Then I choose a right eye Intraocular Pressure Reading Time of "14:00"
    Then I choose a right eye Intraocular Pressure Reading of "5"
    And I add right eye comments of "Right eye comments here"

    Then I choose a left eye Intraocular Pressure Instrument  of "5"

    And I choose left eye Dilation of Yes

    Then I choose a left eye Intraocular Pressure Reading Time of "14:42"
    Then I choose a left eye Intraocular Pressure Reading of "7"
    And I add left eye comments of "Left eye comments here"

    Then I Save the Phasing Event
#
  Scenario: Route 3B: Edit previously edited Phasing from Route 3A

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    And I edit the Last Event

    Then I choose a right eye Intraocular Pressure Instrument  of "3"

    And I choose right eye Dilation of No

    Then I choose a right eye Intraocular Pressure Reading Time of "21:00"
    Then I choose a right eye Intraocular Pressure Reading of "14"
    And I add right eye comments of "Right eye comments here"

    Then I choose a left eye Intraocular Pressure Instrument  of "4"

    And I choose left eye Dilation of Yes

    Then I choose a left eye Intraocular Pressure Reading Time of "04:42"
    Then I choose a left eye Intraocular Pressure Reading of "12"
    And I add left eye comments of "Left eye comments here"

    Then I Save the Phasing Event

  Scenario: Route 3C: Delete previously created/edited Phasing From from Route3A/3B

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    And I delete the Last Event

  Scenario: Route 4A: Login and fill in a Correspondence

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select Create or View Episodes and Events

    Then I expand the Glaucoma sidebar
    And I add a New Event "Correspondence"

    Then I select Site ID "1"
    And I select Address Target "Gp1"
    Then I choose a Macro of "site1"

    And I select Clinic Date "7"

    And I choose CC Target "Patient19434"

    Given I add a New Enclosure of "Test Enclosure"

    Then I Save the Correspondence Draft and confirm it has been created successfully

  Scenario: Route 4B: Edit previously edited Correspondence from Route 4A

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    And I edit the Last Event

    Then I select Site ID "2"
    And I select Address Target "Patient19434"

    And I select Clinic Date "11"

    And I choose CC Target "Gp1"

    Given I add a New Enclosure of "Test Enclosure EDIT"

    Then I Save the Correspondence Draft

  Scenario: Route 4C: Delete previously created/edited Correspondence From from Route 4A/4B

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    And I delete the Last Event

  Scenario: Route 5A: Login and create a New Intravitreal Event

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Cataract sidebar
    And I add a New Event "Intravitreal"

    Then I select Add Left Side
    Then I choose Right Anaesthetic Type of Topical
    Then I choose Right Anaesthetic Type of LA

    Then I choose Right Anaesthetic Delivery of Retrobulbar

    And I choose Right Anaesthetic Agent "5"

    Then I choose Left Anaesthetic Type of Topical
    Then I choose Left Anaesthetic Type of LA

    Then I choose Left Anaesthetic Delivery of Retrobulbar

    And I choose Left Anaesthetic Agent "1"

    Then I choose Right Pre Injection Antiseptic "1"
    Then I choose Right Pre Injection Skin Cleanser "2"
    And I tick the Right Pre Injection IOP Lowering Drops checkbox
    Then I choose Right Pre Injection IOP Lowering Drops "1"
    Then I choose Right Drug "7"
    And I enter "2" number of Right injections
    Then I enter Right batch number "123"

    Then I choose Right Injection Given By "1"
    And I enter a Right Injection time of "09:30"

    Then I choose Left Pre Injection Antiseptic "1"
    Then I choose Left Pre Injection Skin Cleanser "2"
    And I tick the Left Pre Injection IOP Lowering Drops checkbox
    Then I choose Left Pre Injection IOP Lowering Drops "1"
    Then I choose Left Drug "7"
    And I enter "2" number of Left injections
    Then I enter Left batch number "123"

    Then I choose Left Injection Given By "1"
    And I enter a Left Injection time of "09:30"

    Then I choose A Right Lens Status of "1"
    And I choose Right Counting Fingers Checked Yes


    And I choose Right IOP Needs to be Checked No
    Then I choose Right Post Injection Drops "1"

    Then I choose A Left Lens Status of "1"
    And I choose Left Counting Fingers Checked Yes


    And I choose Left IOP Needs to be Checked No
    Then I choose Left Post Injection Drops "1"

    And I select Right Complications "5"
    And I select Left Complications "5"

    Then I Save the Intravitreal injection

  Scenario: Route 5B: Edit previously edited Intravitreal from Route 5A

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    And I edit the Last Event

    Then I choose Right Pre Injection Antiseptic "1"
    Then I choose Right Pre Injection Skin Cleanser "1"

    Then I choose Right Drug "7"
    And I enter "2" number of Right injections
    Then I enter Right batch number "567"

    Then I choose Right Injection Given By "1"
    And I enter a Right Injection time of "09:30"

    Then I choose Left Pre Injection Antiseptic "1"
    Then I choose Left Pre Injection Skin Cleanser "2"

    Then I choose Left Drug "2"
    And I enter "1" number of Left injections
    Then I enter Left batch number "789"

    Then I choose Left Injection Given By "3"
    And I enter a Left Injection time of "09:30"

    Then I choose A Right Lens Status of "1"

    And I choose Right Counting Fingers Checked No
    And I choose Right IOP Needs to be Checked Yes

    Then I choose Right Post Injection Drops "1"

    Then I choose A Left Lens Status of "2"

    And I choose Left Counting Fingers Checked Yes
    And I choose Left IOP Needs to be Checked No

    Then I choose Left Post Injection Drops "2"

    And I select Right Complications "2"
    And I select Left Complications "2"

    Then I Save the Intravitreal injection

  Scenario: Route 5C: Delete previously created/edited Correspondence From from Route 5A/5B

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    And I delete the Last Event

  Scenario: Route 6A: Login and create a new Examination Event: Site 1:Queens, Firm:1 Anderson Cataract

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for hospital number "1009465"

    Then I select the Latest Event
    Then I expand the Cataract sidebar
    And I add a New Event "Examination"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    And I choose to expand the Comorbidities section
    Then I Add a Comorbiditiy of "4"

    Then I choose to expand the Visual Function section

    Then I select a Left RAPD
    And I add Left RAPD comments of "Left RAPD Automation test comments"

    Then I select a Right RAPD
    And I add Right RAPD comments of "Left RAPD Automation test comments"

    Then I choose to expand the Colour Vision section
    And I choose a Left Colour Vision of "1"
    And I choose A Left Colour Vision Value of "8"
    And I choose a Right Colour Vision of "2"
    And I choose A Right Colour Vision Value of "4"

    Then I choose to expand the Visual Acuity section
    And I select a Visual Acuity of "2"
    Then I choose a left Visual Acuity Snellen Metre "4" and a reading method of "5"
    Then I choose a right Visual Acuity Snellen Metre "4" and a reading method of "5"

    Then I choose to expand the Intraocular Pressure section
    Then I choose a left Intraocular Pressure of "19" and Instrument "2"
    Then I choose a right Intraocular Pressure of "29" and Instrument "2"

    Then I choose to expand the Dilation section
    Then I choose left Dilation of "2" and drops of "5"
    Then I choose right Dilation of "6" and drops of "3"

    Then I choose to expand the Refraction section
    Then I enter left Refraction details of Sphere "1" integer "6" fraction "0.75"
    And I enter left cylinder details of of Cylinder "-1" integer "7" fraction "0.75"
    And I enter a left type of "5"
    Then I enter left Axis degrees of "38"

    Then I enter right Refraction details of Sphere "1" integer "3" fraction "0.50"
    And I enter right cylinder details of of Cylinder "-1" integer "4" fraction "0.25"
    Then I enter right Axis degrees of "145"
    And I enter a right type of "1"

    Then I choose to expand the Conclusion section
    And I choose a Conclusion option of "booked for first eye, "

    Then I Save the Examination and confirm it has been created successfully

  Scenario: Route 6B: Edit previously created Examination Event: Site 1:Queens, Firm:1 Anderson Cataract

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    And I edit the Last Event

    And I choose to expand the Comorbidities section
    Then I Add a Comorbiditiy of "2"

    Then I choose to expand the Dilation section
    Then I choose left Dilation of "1" and drops of "4"
    Then I choose right Dilation of "1" and drops of "2"

    Then I Save the Examination

  Scenario: Route 6C: Delete previously created/edited Examination From from Route 6A/6B

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    And I delete the Last Event

  Scenario: Route 7A: Login and create a Laser event

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event
    Then I expand the Glaucoma sidebar
    And I add a New Event "Laser"

    Then I select a Laser site ID "1"
    And I select a Laser of "2"
    And I select a Laser Operator of "2"
    Then I select a Right Procedure of "62"
    Then I select a Left Procedure of "363"

    Then I save the Laser Event

  Scenario: Route 7B: Edit previously created Laser event

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    And I edit the Last Event

    And I select a Laser Operator of "3"
    Then I select a Right Procedure of "370"
    Then I select a Left Procedure of "176"

    Then I save the Laser Event

  Scenario: Route 7C: Delete previously created/edited Laser From from Route 7A/7B

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    And I delete the Last Event
#
  Scenario: 8A Login and create a new Prescription

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Prescription"

    Then I select a Common Drug "75"

    Then I enter a Dose of "2" drops
    And I enter a route of "1"

    And I enter a frequency of "4"
    Then I enter a duration of "1"
    Then I enter a eyes option "1"

    Then I Save the Prescription Draft and confirm it has been created successfully

  Scenario: 8B Login and edit previously created new Prescription

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    And I edit the Last Event

    Then I enter a Dose of "4" drops
    And I enter a route of "8"

    And I enter a frequency of "4"
    Then I enter a duration of "3"

    Then I Save the Prescription Draft

  Scenario: Route 8C: Delete previously created/edited Prescription From from Route 8A/8B

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    And I delete the Last Event

  Scenario: Route 9A: Login and create a Operation Booking Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "OpBooking"

    Then I select Diagnosis Eyes of "Left"
    And I select a Diagnosis of "30041005"
    Then I select Operation Eyes of "Left"
    And I select a Procedure of "41"

    Then I select No to Consultant required

    And I select a Anaesthetic type "Topical"

    Then I select Yes to a Post Operative Stay
    Then I select No to a Post Operative Stay

    And I select a Operation Site of "1"

    Then I select a Priority of Urgent

    And I select a decision date of "14"

    Then I select Save and Schedule now

    And I select OK to Duplicate procedure if requested

    And I select an Available theatre slot date
    And I select an Available session time

    Then I confirm the operation slot

  Scenario: 9B Login and edit previously created new Operation Booking, Consultant Error Check

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    And I edit the Last Event

    Then I select Diagnosis Eyes of "Right"

    Then I select Yes to Consultant required

    Then I select Save

    And I select OK to Duplicate procedure if requested

    Then I confirm that You must change the session or cancel the booking error is displayed

  Scenario: Route 9C: Delete previously created/edited Prescription From from Route 9A/9B

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    And I delete the Last Event

  Scenario: Route 10A: Login and create a Operation Booking Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "OpBooking"

    Then I select Diagnosis Eyes of "Left"
    And I select a Diagnosis of "30041005"
    Then I select Operation Eyes of "Left"
    And I select a Procedure of "41"

    Then I select Yes to Consultant required

    And I select a Anaesthetic type "Topical"

    Then I select Yes to a Post Operative Stay
    Then I select No to a Post Operative Stay

    And I select a Operation Site of "1"

    Then I select a Priority of Urgent

    And I select a decision date of "14"

    Then I select Save and Schedule now

    And I select OK to Duplicate procedure if requested

    And I select an Available theatre slot date
    And I select an Available session time

    Then I confirm the operation slot

  Scenario: 10B Login and edit previously created new Operation Booking

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    And I edit the Last Event

    Then I select Diagnosis Eyes of "Right"

    Then I select a Priority of Urgent

    Then I select Save

    And I select OK to Duplicate procedure if requested

  Scenario: Route 10C: Delete previously created/edited Prescription From from Route 10A/10B

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    And I delete the Last Event
