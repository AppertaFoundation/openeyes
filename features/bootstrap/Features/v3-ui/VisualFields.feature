@correspondence @regression
Feature: Visual Fields Test
  @EXAM
  @javascript

  Scenario Outline:
  Route 1: Login and create a therapy application event, check the other option appears after already being selected
  Site :  Kings
  Firm :  MR Clinic (Medical Retina)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Change Firm
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for patient name last name "<lastName>" and first name "<firstName>"

    And I add a New Event "<event>"
    Then I select condition ability "<ability>"
    Then I select glasses yes
    Then I write visual field comments "<comment>"

    Given I select result "<result>"
    Then I write result comment "<result_other>"

    Then I save the VisualField Event and confirm it has been created successfully
    Then I delete the event


    Examples:
      |uname|pwd|siteName/Number|firmName/Number           |lastName|firstName|event                  |ability|comment                |result        |result_other|
      |admin|admin|Kings        |MR Clinic (Medical Retina)|coffin, |violet   |OphInVisualfields      |Age    |Testing Testing Testing|Arcuate defect|Nothing     |
