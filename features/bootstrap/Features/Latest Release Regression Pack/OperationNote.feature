@opnote
Feature: Create New Operation Note Event
  Regression coverage of this event is approx TBC%

  Scenario: Route 1: Login and create a Operation Note Event
            Site 2: Kings
            Firm 3: Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "OpNote"

    Then I select an Emergency Operation Note

    And I select Create Operation Note

    Then I select Procedure Right Eye
    Then I select Procedure Left Eye

    And I select a Procedure of "41"





