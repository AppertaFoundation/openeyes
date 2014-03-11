@scenario @regression
Feature: Open Eyes Login and Patient Diagnosis Screen Template
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario: Route 1A: Login and add an Allergy on Patient View Page for subsequent tests

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"
    Then I search for hospital number "1009465"

    Then I Add an Ophthalmic Diagnosis selection of "193570009"
    And I select that it affects eye "Left"
    And I select a Opthalmic Diagnosis date of day "18" month "6" year "2012"
    Then I save the new Opthalmic Diagnosis

    Then I Add an Systemic Diagnosis selection of "195967001"
    And I select that it affects Systemic side "Left"
    And I select a Systemic Diagnosis date of day "18" month "6" year "2012"

    Then I save the new Systemic Diagnosis

    Then I Add a Previous Operation of "1"
    And I select that it affects Operation side "Left"
    And I select a Previous Operation date of day "9" month "9" year "2012"
    Then I save the new Previous Operation

    Then I edit the CVI Status "4"
    And I select a CVI Status date of day "18" month "6" year "2012"
    Then I save the new CVI status

    Then I Remove existing Allergy
    Then I Add Allergy "5" and Save
    # 5 = Tetracycline

    And I Add a Family History of relative "1" side "3" condition "1" and comments "Family History Comments" and Save

  Scenario: Route 1B: Check Allergy warning on Intravitreal

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Cataract sidebar
    And I add a New Event "Intravitreal"

    Then a check is made that the Allergy "Tetracycline" warning is displayed

  Scenario: Route 1C: Check Allergy warning on Prescription

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Cataract sidebar
    And I add a New Event "Prescription"

    Then a check is made that the Allergy "Tetracycline" warning is displayed

  Scenario: Route 1D: Check Allergy warning on Op Note

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Cataract sidebar
    And I add a New Event "OpNote"

    Then a check is made that the Allergy "Tetracycline" warning is displayed

  Scenario: Route 2A: Login and create a new Examination Event
            Create a Correspondence event including Findings from the Examination

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event
    Then I expand the Glaucoma sidebar
    And I add a New Event "Examination"

    Then I select a History of Blurred Vision, Mild Severity, Onset 1 Week, Left Eye, 1 Week

    And I choose to expand the Comorbidities section
    Then I Add a Comorbiditiy of "1"
    Then I Add a Comorbiditiy of "2"
    Then I Add a Comorbiditiy of "3"
    Then I Add a Comorbiditiy of "4"
    Then I Add a Comorbiditiy of "5"
    Then I Add a Comorbiditiy of "6"
    Then I Add a Comorbiditiy of "7"
    Then I Add a Comorbiditiy of "8"
    Then I Add a Comorbiditiy of "9"
    Then I Add a Comorbiditiy of "10"
    Then I Add a Comorbiditiy of "11"
    Then I Add a Comorbiditiy of "12"
    Then I Add a Comorbiditiy of "13"
    Then I Add a Comorbiditiy of "14"
    Then I Add a Comorbiditiy of "15"

    Then I remove all comorbidities

    Then I choose to expand the Refraction section

    Then I enter left Refraction details of Sphere "1" integer "6" fraction "0.75"
    And I enter left cylinder details of of Cylinder "-1" integer "7" fraction "0.75"

    Then I enter left Axis degrees of "145"
    Then I enter left Axis degrees of "145"

    And I enter a left type of "5"

    Then I enter right Refraction details of Sphere "-1" integer "9" fraction "0.75"
    And I enter right cylinder details of of Cylinder "1" integer "5" fraction "0"

    Then I enter right Axis degrees of "38"
    Then I enter right Axis degrees of "38"
    And I enter a right type of "3"

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

    Then I choose to remove left Dilation treatment

    Then I Save the Examination and confirm it has been created successfully

    Then a check is made that a left Axis degrees of "145" was entered
    Then a check is made that a right Axis degrees of "38" was entered

  Scenario: 2B Login and fill in a Correspondence

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

    Then I choose an Introduction of "site21"
    And I add Findings of "examination334"
    And I choose a Diagnosis of "site541"
    Then I choose a Management of "site181"
    And I choose Drugs "site301"
    Then I choose Outcome "site341"

    And I choose CC Target "Patient19434"

    Given I add a New Enclosure of "Test Enclosure"

    Then I Save the Correspondence Draft and confirm it has been created successfully

  Scenario: 3B: Login and create a new Prescription. Check for Validation error upon Saving
            Site 1:Queens
            Firm 3:Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Prescription"

    Then I Save the Prescription Draft

    And I confirm the prescription validation error has been displayed


