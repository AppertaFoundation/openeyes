@MultipleSiteAndFirm
Feature: Create an Episode then Change Site and Firm and create another Episode

  Scenario Outline: Multiple Sites and Firms Template

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "<site>"
    Then I select a firm of "1"

    Then I search for patient name last name "<last>" and first name "<first>"

    Then I select Create or View Episodes and Events
    Then I select Add First New Episode and Confirm

    And I select Change Firm
    And I select Site "<site>"
    Then I select a firm of "2"
    And I Add a New Episode and Confirm



  Examples:
    | environment | username | password | site | last   | first  |
    | master      | admin    | admin    | 2    | Coffin,| Violet |


  # Site ID's:
  # Queens-1, Kings-2