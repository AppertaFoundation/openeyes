@diagnosis @regression
Feature: Open Eyes Login and Patient Diagnosis Screen Template
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario: Route 1: Login and add Opthamlmic Diagnosis, Systemic Diagnosis, CVI, Medication and other Allergies

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

    And I Add a Family History of relative "1" side "3" condition "1" and comments "Family History Comments" and Save


  Scenario: Route 2: Login and add Opthamlmic Diagnosis, Systemic Diagnosis, CVI, Medication and other Allergies

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"
    Then I search for hospital number "1009465"

    Then I Add an Ophthalmic Diagnosis selection of "95217000"
    And I select that it affects eye "Right"
    And I select a Opthalmic Diagnosis date of day "4" month "3" year "2012"
    Then I save the new Opthalmic Diagnosis

    Then I Add an Systemic Diagnosis selection of "46635009"
    And I select that it affects Systemic side "Right"
    And I select a Systemic Diagnosis date of day "4" month "1" year "2012"

    Then I save the new Systemic Diagnosis

    Then I Add a Previous Operation of "4"
    And I select that it affects Operation side "Right"
    And I select a Previous Operation date of day "4" month "1" year "2012"
    Then I save the new Previous Operation

    Then I edit the CVI Status "3"
    And I select a CVI Status date of day "4" month "1" year "2012"
    Then I save the new CVI status

    Then I Remove existing Allergy
    Then I confirm the patient has no allergies and Save

    And I Add a Family History of relative "5" side "2" condition "5" and comments "Family History Comments" and Save


  Scenario: Route 3: Login and add Opthamlmic Diagnosis, Systemic Diagnosis, CVI, Medication and other Allergies

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "2"
    Then I search for hospital number "1009465"

    Then I Add an Ophthalmic Diagnosis selection of "24010005"
    And I select that it affects eye "Both"
    And I select a Opthalmic Diagnosis date of day "9" month "7" year "2012"
    Then I save the new Opthalmic Diagnosis

    Then I Add an Systemic Diagnosis selection of "414545008"
    And I select that it affects Systemic side "Both"
    And I select a Systemic Diagnosis date of day "9" month "7" year "2012"

    Then I save the new Systemic Diagnosis

    Then I Add a Previous Operation of "4"
    And I select that it affects Operation side "Both"
    And I select a Previous Operation date of day "9" month "7" year "2012"
    Then I save the new Previous Operation

    Then I edit the CVI Status "2"
    And I select a CVI Status date of day "4" month "1" year "2012"
    Then I save the new CVI status

    Then I Remove existing Allergy
    Then I Add Allergy "12" and Save

    And I Add a Family History of relative "4" side "3" condition "2" and comments "Family History Comments" and Save

