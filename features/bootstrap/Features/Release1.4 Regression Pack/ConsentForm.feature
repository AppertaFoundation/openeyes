@consent @regression
Feature: Create New Consent Form
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario: Route 1: Login and create a new Consent Form

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

    And I select the Information leaflet checkbox

    Then I save the Consent Form

  Scenario: Route 2: Login and create a new Consent Form

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
    And I choose Type "2"

    Then I choose Procedure eye of "Left"
    And I choose an Anaesthetic type of LA
    And I add a common procedure of "129"

    Then I choose Permissions for images Yes

    Then I save the Consent Form