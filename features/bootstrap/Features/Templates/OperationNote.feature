
Feature: Create New Operation Note TEMPLATE
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

    And I select a common Procedure of "41"

    Then I choose Anaesthetic Type of Topical
    Then I choose Anaesthetic Type of LA
    Then I choose Anaesthetic Type of LAC
    Then I choose Anaesthetic Type of LAS
    Then I choose Anaesthetic Type of GA

    And I choose Given by Anaesthetist
    And I choose Given by Surgeon
    And I choose Given by Nurse
    And I choose Given by Anaesthetist Tehnician
    And I choose Given by Other

    Then I choose Delivery by Retrobulbar
    Then I choose Delivery by Peribulbar
    Then I choose Delivery by Subtenons
    Then I choose Delivery by Subconjunctival
    Then I choose Delivery by Topical
    Then I choose Delivery by Topical & Intracameral
    Then I choose Delivery by Other

    Then I choose an Anaesthetic Agent of "1"

    Then I choose a Complication of "1"

    And I add Anaesthetic comments of "Test comments"

    Then I choose a Surgeon of "2"
    And I choose a Supervising Surgeon of "3"
    Then I choose an Assistant of "3"

    Then I choose Per Operative Drugs of "1"

    And I choose Operation comments of "Test Comments"

    Then I choose Post Op instructions of "1"

    Then I save the Operation Note