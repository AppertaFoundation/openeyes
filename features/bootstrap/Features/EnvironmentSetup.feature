@setup
Feature: Environment Setup on clean database
  This feature is to be run first on a clean database
  It sets up 3 Firms Glaucoma, Medical Retina and Cataract
  Each with an event setup ready for the regression tag to be run

  Scenario: Environment Setup Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "2"

    Then I search for hospital number "1009465"

    Then I select Add First New Episode and Confirm
    Then I expand the Glaucoma sidebar

    And I add a New Event "Therapy"

  Scenario: Environment Setup Medical Retinal

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "4"

    Then I search for hospital number "1009465"

    Then I select Create or View Episodes and Events
    And I select Add Episode from the sidebar
    Then I expand the Medical Retinal sidebar

    And I add a New Event "Therapy"

  Scenario: Environment Setup Cataract

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "1"

    Then I search for hospital number "1009465"

    Then I select Create or View Episodes and Events
    And I select Add Episode from the sidebar
    Then I expand the Cataract sidebar
    And I add a New Event "Therapy"


  Scenario: Environment Setup Support Firm

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "5"

    Then I search for hospital number "1009465"

    Then I select Create or View Episodes and Events
    And I select Add Episode from the sidebar
    Then I expand the Support Firm sidebar
    And I add a New Event "Consent"


