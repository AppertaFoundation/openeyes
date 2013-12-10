@editdelete
Feature: These tests set up Events, Edit and Delete them.

#  Scenario: Route 1A: Login and create a Anaesthetic Satisfaction Audit Regression: Site 2 Kings, Firm 3 Anderson Glaucoma
#
#    Given I am on the OpenEyes "master" homepage
#    And I enter login credentials "admin" and "admin"
#    And I select Site "2"
#    Then I select a firm of "3"
#
#    Then I search for patient name last name "Coffin," and first name "Violet"
#
#    Then I select the Latest Event
#
#    Then I expand the Glaucoma sidebar
#    And I add a New Event "Satisfaction"
#
#    Then I select an Anaesthetist "no"
#    And I select Satisfaction levels of Pain "2" Nausea "3"
#
#    And I tick the Vomited checkbox
#
#    Then I select Vital Signs of Respiratory Rate "3" Oxygen Saturation "3" Systolic Blood Pressure "4"
#    And I select Vital Signs of Body Temperature "5" and Heart Rate "2" Conscious Level AVPU "2"
#
#    Then I enter Comments "This test is for Site 2 Kings, Firm 3 Anderson Glaucoma"
#
#    And I select the Yes option for Ready to Discharge
#
#    Then I Save the Event
#
#  Scenario: Route 1B: Edit previously created ASA from Route1A
#
#    Given I am on the OpenEyes "master" homepage
#    And I enter login credentials "admin" and "admin"
#    And I select Site "2"
#    Then I select a firm of "3"
#
#    Then I search for patient name last name "Coffin," and first name "Violet"
#
#    Then I select the Latest Event
#
#    And I edit the Last Event
#
#    Then I select an Anaesthetist "non"
#    And I select Satisfaction levels of Pain "4" Nausea "1"
#
#    And I untick the Vomited checkbox
#
#    Then I select Vital Signs of Respiratory Rate "4" Oxygen Saturation "1" Systolic Blood Pressure "5"
#    And I select Vital Signs of Body Temperature "1" and Heart Rate "5" Conscious Level AVPU "5"
#
#    Then I enter Comments "Route 1 ASA Edit and Save Test"
#
#    And I select the No option for Ready to Discharge
#
#    Then I Save the Event
#
#  Scenario: Route 1C: Delete previously created/edited ASA from Route1A/1B
#
#    Given I am on the OpenEyes "master" homepage
#    And I enter login credentials "admin" and "admin"
#    And I select Site "2"
#    Then I select a firm of "3"
#
#    Then I search for patient name last name "Coffin," and first name "Violet"
#
#    Then I select the Latest Event
#
#    And I delete the Last Event
#
#  Scenario: Route 2A: Login and create a new Consent Form
#
#    Given I am on the OpenEyes "master" homepage
#    And I enter login credentials "admin" and "admin"
#    And I select Site "2"
#    Then I select a firm of "3"
#
#    Then I search for hospital number "1009465"
#
#    Then I select the Latest Event
#
#    Then I expand the Glaucoma sidebar
#    And I add a New Event "Consent"
#    Then I select Unbooked Procedures
#    Then I select Add Consent Form
#    And I choose Type "1"
#
#    Then I choose Procedure eye of "Both"
#    And I choose an Anaesthetic type of LA
#    And I add a common procedure of "127"
#
#    Then I choose Permissions for images No
#
#    Then I save the Consent Form
#
#  Scenario: Route 2B: Edit previously created Consent from Route2A
#
#    Given I am on the OpenEyes "master" homepage
#    And I enter login credentials "admin" and "admin"
#    And I select Site "2"
#    Then I select a firm of "3"
#
#    Then I search for patient name last name "Coffin," and first name "Violet"
#
#    Then I select the Latest Event
#
#    And I edit the Last Event
#
#    And I choose Type "2"
#
#    Then I choose Procedure eye of "Right"
#    And I choose an Anaesthetic type of LAC
#
#    Then I choose Permissions for images No
#
#    Then I save the Consent Form
#
#  Scenario: Route 2C: Delete previously created/edited Consent From from Route2A/2B
#
#    Given I am on the OpenEyes "master" homepage
#    And I enter login credentials "admin" and "admin"
#    And I select Site "2"
#    Then I select a firm of "3"
#
#    Then I search for patient name last name "Coffin," and first name "Violet"
#
#    Then I select the Latest Event
#
#    And I delete the Last Event

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



