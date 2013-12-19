@scenario
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


