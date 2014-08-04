@correspondence @regression
Feature: Create New Correspondence
         Regression coverage of this event is approx 95%

  Scenario: Route 1:Login and fill in a Correspondence Event
            Site 1:  Queens
            Firm 3:  Anderson Glaucoma

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

    Then I choose an Introduction of "site21"
    And I choose a Diagnosis of "site541"
    Then I choose a Management of "site181"
    And I choose Drugs "site301"
    Then I choose Outcome "site341"

    And I choose CC Target "Patient19434"

    Given I add a New Enclosure of "Test Enclosure"

    Then I Save the Correspondence Draft and confirm it has been created successfully

  Scenario: Route 2:Login and fill in a Correspondence Event
            Site 1:  Queens
            Firm 3:  Anderson Glaucoma
            Alternative Diagnosis, Management, Drugs & Outcome options

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

    Then I choose an Introduction of "site21"
    And I choose a Diagnosis of "site81"
    Then I choose a Management of "site161"
    And I choose Drugs "site261"
    Then I choose Outcome "site321"

    And I choose CC Target "Patient19434"

    Given I add a New Enclosure of "Test Enclosure"

    Then I Save the Correspondence Draft and confirm it has been created successfully

  Scenario: Route 3:Login and fill in a Correspondence Event
            Site 1:  Queens
            Firm 3:  Anderson Glaucoma
            Alternative Diagnosis, Management, Drugs & Outcome options

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

    Then I choose an Introduction of "site41"
    And I choose a Diagnosis of "site541"
    Then I choose a Management of "site141"
    And I choose Drugs "site281"
    Then I choose Outcome "site361"

    And I choose CC Target "Patient19434"

    Given I add a New Enclosure of "Test Enclosure"

    Then I Save the Correspondence Draft and confirm it has been created successfully

  Scenario: Route 4:Login and fill in a Correspondence Event
            Site 1:  Queens
            Firm 3:  Anderson Glaucoma
            Alternative Diagnosis, Management, Drugs & Outcome options

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

    Then I choose an Introduction of "site61"
    And I choose a Diagnosis of "site81"
    Then I choose a Management of "site121"
    And I choose Drugs "site261"
    Then I choose Outcome "site401"

    And I choose CC Target "Patient19434"

    Given I add a New Enclosure of "Test Enclosure"

    Then I Save the Correspondence Draft and confirm it has been created successfully

  Scenario: Route 5:Login and fill in a Correspondence Event
            Site 1:  Queens
            Firm 3:  Anderson Glaucoma
            Saving without mandatory fields validation tests

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select Create or View Episodes and Events

    Then I expand the Glaucoma sidebar
    And I add a New Event "Correspondence"

    Then I Save the Correspondence Draft

    Then I Confirm that the Mandatory Correspondence fields validation error messages are displayed