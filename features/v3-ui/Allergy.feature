@examination @regression
Feature: Allergy test
  @EXAM
  @javascript

  Scenario Outline:
  Route 1: Login and create an Allergy element, check the other option appears after already being selected
  Site :  Kings
  Firm :  MR Clinic (Medical Retina)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"

    Then I search for patient name last name "<lastName>" and first name "<firstName>"

    And I add a New Event "<event>"
    And I select Close All elements
    And I add Allergy Element
    Then I Add Allergy "<allergy1>"
    Then I Add Allergy "<allergy2>"

    Then I Save the Event and confirm it has been created successfully
    Then I delete the event
    Then I logout

    Examples:
      |uname|pwd  |lastName|firstName|event            | allergy1 | allergy2 |
      |admin|admin|Coffin, |Violet   |OphCiExamination | Latex  | Other |