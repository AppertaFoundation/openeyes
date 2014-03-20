@rbac @regression
Feature: Open Eyes Login RBAC user levels

  Scenario: Route 1: Level 0: User with no login rights

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "level0" and "password"
    And I confirm that an Invalid Login error message is displayed

  Scenario: Route 2: Login and add Opthamlmic Diagnosis, Systemic Diagnosis, CVI, Medication and other Allergies

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "level1" and "password"
    And I select Site "1"
    Then I select a firm of "1"
    Then I search for hospital number "1009465"