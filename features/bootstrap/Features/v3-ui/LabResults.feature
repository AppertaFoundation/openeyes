@examination @regression
Feature: Lab Results test
  @EXAM
  @javascript

  Scenario Outline:
  Route 1: Login and create Lab Results event, filling the all the form and then wait for 5 seconds. Then save the Lab Result Issue and confirm.
  Site :  Kings
  Firm :  MR Clinic (Medical Retina)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"

    Then I search for patient name last name "<lastName>" and first name "<firstName>"

    And I add a New Event "<event>"
    Then I select Lab Results type "<type>"
    And I select time of recording "<time>"
    And I select the result of "<result>"
    Then I select a comment message of "<comment>"

    Then I save the Lab Result and confirm
    And I delete the event
    Then I logout


    Examples:
      |uname|pwd  |lastName|firstName|event           | type  | time | result | comment                            |
      |admin|admin|Coffin, |Violet   |OphInLabResults | INR   | 1:00 | 2.1    | This the result message for testing|