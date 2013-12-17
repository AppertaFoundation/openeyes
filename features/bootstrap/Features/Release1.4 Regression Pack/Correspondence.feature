@correspondence @regression
Feature: Create New Correspondence
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario: Login and fill in a Correspondence THIS TEST NEEDS SAMPLE DATE FOR INTROS - OUTCOME

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

#    Then I choose an Introduction of "site21"
#    And I add Findings of "examination334"
#    And I choose a Diagnosis of "site541"
#    Then I choose a Management of "site181"
#    And I choose Drugs "site301"
#    Then I choose Outcome "site341"

    And I choose CC Target "Patient19434"

    Given I add a New Enclosure of "Test Enclosure"

    Then I Save the Correspondence Draft
