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
    And I select Change Firm
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for patient name last name "<lastName>" and first name "<firstName>"

    And I add a New Event "<event>"
    And I select Close All elements
    And I add Allergy Element
    Then I Add Allergy "<allergy>"
    Then I Add Allergy "<allergy>"

    Then I Save the Event and confirm it has been created successfully
    Then I delete the event

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number           |lastName|firstName|event            | allergy |
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |OphCiExamination | Other   |