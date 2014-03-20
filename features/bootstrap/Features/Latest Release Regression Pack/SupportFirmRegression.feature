@SupportFirm
Feature: Create New Correspondence using Support Firm
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario: Login and create a new Correspondence using Support Firm

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "5"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Support Firm sidebar
    And I add a New Event "Correspondence"

    Then I select Site ID "2"
    And I select Address Target "Gp1"
    Then I choose a Macro of "site1"

    And I select Clinic Date "7"

#    Then I choose an Introduction of "site21"
#    And I add Findings of "examination334"
#    And I choose a Diagnosis of "site541"
#    Then I choose a Management of "site181"
#    And I choose Drugs "site301"
#    Then I choose Outcome "site341"

#   AWAITING NEW DATA SET! The above fields are greyed out in my data set :(

    And I choose CC Target "patient"

    And I add a New Enclosure

    Then I Save the Correspondence Draft

  Scenario: Login with Support Firm and add Opthamlmic Diagnosis, Systemic Diagnosis, CVI, Medication and other Allergies

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "5"

    Then I search for hospital number " 1009465 "

    Then I Add an Ophthalmic Diagnosis selection of "193570009 "
    And I select that it affects eye "Both"
    And I select a Opthalmic Diagnosis date of day "18" month "6" year "2012"
    Then I save the new Opthalmic Diagnosis

    Then I Add an Systemic Diagnosis selection of "195967001"
    And I select that it affects Systemic side "Both"
    And I select a Systemic Diagnosis date of day "18" month "6" year "2012"

    Then I save the new Systemic Diagnosis
    Then I Add a Previous Operation of "1"
    And I select that it affects Operation side "Both"
    And I select a Previous Operation date of day "18" month "6" year "2012"
    Then I save the new Previous Operation

    And I Add Medication details medication "3" route "2" frequency "8" date from "1" and Save

    Then I edit the CVI Status "4"
    And I select a CVI Status date of day "18" month "6" year "2012"
    Then I save the new CVI status

    Then I Remove existing Allergy
    Then I Add Allergy "5" and Save

    And I Add a Family History of relative "1" side "3" condition "1" and comments "Family History Comments" and Save



