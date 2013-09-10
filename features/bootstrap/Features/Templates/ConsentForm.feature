@consent
Feature: Create New Examination
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario Outline: Login and create a new Examination Event

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "<hospnumber>"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Consent"
    Then I select Add Consent Form



  Examples: User details
    | environment   | site      | username | password     | hospnumber   | nhs        | last           | first         | EventType     |
    | master        | 1         | admin    | admin        | 1009465      | 8821388753 | Coffin,        | Violet        | Examination   |
