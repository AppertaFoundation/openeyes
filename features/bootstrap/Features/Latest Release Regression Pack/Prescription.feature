@prescription @regression
Feature: Create New Prescription
         Regression coverage of this event is approx 95%

  Scenario: Route 1: Login and create a new Prescription
            Site 1:Queens
            Firm 3:Anderson Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Prescription"

    Then I select a Common Drug "75"
    And I select a Standard Set of "10"

    Then I enter a Dose of "2" drops
    And I enter a route of "1"

    And I enter a frequency of "4"
    Then I enter a duration of "1"
    Then I enter a eyes option "1"

    And I add Prescription comments of "TEST COMMENTS"

    Then I Save the Prescription Draft and confirm it has been created successfully

  Scenario: Route 2: Login and create a new Prescription
            Site 2:Kings
            Firm 3:Anderson Cataract

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "1"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Cataract sidebar
    And I add a New Event "Prescription"

    Then I choose to filter by type "28"
    And I select the No preservative checkbox

    Then I select a Common Drug "280"
    And I select a Standard Set of "7"

    Then I enter a Dose of "3" drops
    And I enter a route of "1"

    And I enter a frequency of "4"
    Then I enter a duration of "1"
    Then I enter a eyes option "1"

    And I add Prescription comments of "TEST COMMENTS"

    Then I Save the Prescription Draft and confirm it has been created successfully

  Scenario: Route 3: Login and create a new Prescription
            Site 2:Kings
            Firm 4:Anderson Medical Retinal
            Add two Tapers, Remove Taper

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "4"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Medical Retinal sidebar
    And I add a New Event "Prescription"

    Then I select a Common Drug "176"
    And I select a Standard Set of "11"

    Then I enter a Dose of "2" drops
    And I enter a route of "3"

    And I enter a frequency of "5"
    Then I enter a duration of "3"

    Then I add a Taper
    And I enter a first Taper does of "4"
    Then I enter a first Taper frequency of "2"
    And I enter a first Taper duration of "6"

    Then I add a Taper
    And I enter a second Taper dose of "3"
    Then I enter a second Taper frequency of "7"
    And I enter a second Taper duration of "2"

    Then I add a Taper
    Then I remove the last Taper

    And I add Prescription comments of "TEST COMMENTS"

    Then I Save the Prescription Draft and confirm it has been created successfully

  Scenario: Route 4: Login and create a new Prescription
            Site 1:Queens
            Firm 2:Broom Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "2"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Prescription"

    Then I choose to filter by type "28"
    And I select the No preservative checkbox

    Then I select a Common Drug "569"
    And I select a Standard Set of "10"

    Then I enter a Dose of "2" drops
    And I enter a route of "17"

    And I enter a frequency of "6"
    Then I enter a duration of "6"

    And I add Prescription comments of "TEST COMMENTS"

    Then I Save the Prescription Draft and confirm it has been created successfully


