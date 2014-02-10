@prescription @regression
Feature: Create New Prescription
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario: Login and create a new Prescription

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
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


