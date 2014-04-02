@opnote @regression
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

    And I select a common Procedure of "41"

    Then I choose Anaesthetic Type of Topical

    And I choose Given by Anaesthetist

    Then I choose Delivery by Retrobulbar

    Then I choose an Anaesthetic Agent of "1"

    Then I choose a Complication of "1"

    And I add Anaesthetic comments of "Test comments"

    Then I choose a Surgeon of "2"
    And I choose a Supervising Surgeon of "3"
    Then I choose an Assistant of "3"

    Then I choose Per Operative Drugs of "1"

    And I choose Operation comments of "Test Comments"

    Then I choose Post Op instructions of "1"

    Then I save the Operation Note and confirm it has been created successfully

  Scenario: Route 2: Login and create a Operation Note Event
  Site 2: Kings
  Firm 4: Medical Retinal

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "4"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Medical Retinal sidebar
    And I add a New Event "OpNote"

    Then I select an Emergency Operation Note

    And I select Create Operation Note

    Then I select Procedure Left Eye

    And I select a common Procedure of "321"

    Then I choose Anaesthetic Type of LA

    And I choose Given by Surgeon

    Then I choose Delivery by Peribulbar

    Then I choose an Anaesthetic Agent of "1"

    Then I choose a Complication of "6"

    And I add Anaesthetic comments of "Test comments"

    Then I choose a Surgeon of "3"
    And I choose a Supervising Surgeon of "2"
    Then I choose an Assistant of "2"

    Then I choose Per Operative Drugs of "1"

    And I choose Operation comments of "Test Comments"

    Then I choose Post Op instructions of "1"

    Then I save the Operation Note and confirm it has been created successfully

  Scenario: Route 3: Login and create a Operation Note Event
  Site 1: Queens
  Firm 3: Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "OpNote"

    Then I select an Emergency Operation Note

    And I select Create Operation Note

    Then I select Procedure Right Eye

    And I select a common Procedure of "139"

    Then I choose Anaesthetic Type of LAC

    And I choose Given by Nurse

    Then I choose Delivery by Subtenons

    Then I choose an Anaesthetic Agent of "1"

    Then I choose a Complication of "9"

    And I add Anaesthetic comments of "Test comments"

    Then I choose a Surgeon of "3"
    And I choose a Supervising Surgeon of "3"
    Then I choose an Assistant of "3"

    Then I choose Per Operative Drugs of "1"

    And I choose Operation comments of "Test Comments"

    Then I choose Post Op instructions of "1"

    Then I save the Operation Note and confirm it has been created successfully

  Scenario: Route 4: Login and create a Operation Note Event
  Site 1: Queens
  Firm 2: Broom Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "2"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "OpNote"

    Then I select an Emergency Operation Note

    And I select Create Operation Note

    Then I select Procedure Right Eye

    And I select a common Procedure of "41"

    Then I choose Anaesthetic Type of LAS

    And I choose Given by Anaesthetist Tehnician

    Then I choose Delivery by Subconjunctival

    Then I choose an Anaesthetic Agent of "1"

    Then I choose a Complication of "4"

    And I add Anaesthetic comments of "Test comments"

    Then I choose a Surgeon of "3"
    And I choose a Supervising Surgeon of "2"
    Then I choose an Assistant of "3"

    Then I choose Per Operative Drugs of "1"

    And I choose Operation comments of "Test Comments"

    Then I choose Post Op instructions of "1"

    Then I save the Operation Note and confirm it has been created successfully

  Scenario: Route 5: Login and create a Operation Note Event
  Site 1: Queens
  Firm 4: Medical Retinal

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "4"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Medical Retinal sidebar
    And I add a New Event "OpNote"

    Then I select an Emergency Operation Note

    And I select Create Operation Note

    Then I select Procedure Left Eye

    And I select a common Procedure of "41"

    Then I choose Anaesthetic Type of GA

    And I choose Given by Other

    Then I choose Delivery by Topical
    Then I choose Delivery by Topical & Intracameral
    Then I choose Delivery by Other

    Then I choose an Anaesthetic Agent of "1"

    Then I choose a Complication of "5"

    And I add Anaesthetic comments of "Test comments"

    Then I choose a Surgeon of "3"
    And I choose a Supervising Surgeon of "2"
    Then I choose an Assistant of "3"

    Then I choose Per Operative Drugs of "1"

    And I choose Operation comments of "Test Comments"

    Then I choose Post Op instructions of "1"

    Then I save the Operation Note and confirm it has been created successfully

