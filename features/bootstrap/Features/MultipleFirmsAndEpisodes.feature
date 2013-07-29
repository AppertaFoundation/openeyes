@MultipleSiteAndFirm
Feature: Create an Episode then Change Site and Firm and create another Episode
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario Outline: Multiple Sites and Firms Template

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "<site>"
    Then I select a firm of "<firm>"

    Then I search for patient name last name "<last>" and first name "<first>"

    Then I select Create or View Episodes and Events
    Then I select Add First New Episode and Confirm

    And I select Change Firm
    And I select Site "<site>"
    Then I select a firm of "2"
    And I Add a New Episode and Confirm



  Examples:
    | environment | username | password | site    | firm                     | last   | first  |
    | master      | admin    | admin    | Queens  | Anderson Firm (Cataract) | Coffin,| Violet |


