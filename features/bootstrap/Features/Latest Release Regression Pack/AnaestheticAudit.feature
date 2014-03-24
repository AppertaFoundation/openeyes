@asa @regression
Feature: Anaesthetic Satisfaction Audit Regression Tests
  Regression coverage of this event is 100%
  Across 2 Sites and 4 Firms

  Scenario: Route 1: Login and create a Anaesthetic Satisfaction Audit Regression:
            Site 2:  Kings
            Firm 3:  Anderson Glaucoma

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

    Then I Save the Event and confirm it has been created successfully

  Scenario: Route 2: Login and create a Anaesthetic Satisfaction Audit:
            Site 1:  Queens
            Firm 1:  Anderson Cataract

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Cataract sidebar
    And I add a New Event "Satisfaction"

    Then I select an Anaesthetist "non"
    And I select Satisfaction levels of Pain "5" Nausea "1"

    And I untick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "4" Oxygen Saturation "1" Systolic Blood Pressure "5"
    And I select Vital Signs of Body Temperature "1" and Heart Rate "5" Conscious Level AVPU "5"

    Then I enter Comments "This test is for Site 1 Queens, Firm 1 Anderson Cataract"

    And I select the No option for Ready to Discharge

    Then I Save the Event and confirm it has been created successfully

  Scenario: Route 3: Login and create a Anaesthetic Satisfaction Audit Regression:
            Site 1: Queens
            Firm 2: Broom Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "2"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Satisfaction"

    Then I select an Anaesthetist "no"
    And I select Satisfaction levels of Pain "7" Nausea "2"

    And I tick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "1" Oxygen Saturation "3" Systolic Blood Pressure "3"
    And I select Vital Signs of Body Temperature "4" and Heart Rate "5" Conscious Level AVPU "2"

    Then I enter Comments "This test is for Site 1 Queens, Firm 2 Broom Glaucoma"

    And I select the No option for Ready to Discharge

    Then I Save the Event and confirm it has been created successfully

  Scenario: Route 4: Login and create a Anaesthetic Satisfaction Audit Regression
            Site 2: Kings
            Firm 4: Medical Retinal

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "4"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    Then I expand the Medical Retinal sidebar
    And I add a New Event "Satisfaction"

    Then I select an Anaesthetist "non"
    And I select Satisfaction levels of Pain "0" Nausea "0"

    And I untick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "2" Oxygen Saturation "4" Systolic Blood Pressure "6"
    And I select Vital Signs of Body Temperature "3" and Heart Rate "2" Conscious Level AVPU "2"

    Then I enter Comments "This test is for Site 2 Kings, Firm 4 Medical Retinal"

    And I select the No option for Ready to Discharge

    Then I Save the Event and confirm it has been created successfully

  Scenario: Route 5: Validation Tests
            Ensure that correct validation messages are displayed when the user attempts to save

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for patient name last name "Coffin," and first name "Violet"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Satisfaction"

    Then I Save the Event

    Then I confirm that the ASA Validation error messages have been displayed

