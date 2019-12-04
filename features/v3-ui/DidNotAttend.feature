@examination @regression
Feature: DidNotAttend test
  @EXAM
  @javascript

  Scenario Outline:
  Route 1: Login and create an Did Not Attend element, filling the comments.
  Site :  Kings
  Firm :  MR Clinic (Medical Retina)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"

    Then I search for patient name last name "<lastName>" and first name "<firstName>"

    And I add a New Event "<event>"
    Then I add not attend comments of "<comments>"
    Then I Save the Event and confirm it has been created successfully
    Then I delete the event
    Then I logout

    Examples:
      |uname|pwd  |lastName|firstName|event             | comments                                          |
      |admin|admin|Coffin, |Violet   |OphCiDidNotAttend | (Testing) She is busy and cancel the appointment. |