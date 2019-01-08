@examination @regression
Feature: Document test
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
    Then I select the event "<event_id>"
    Then I edit the event

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number           |lastName|firstName|      event_id      |
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |    4686458         |
