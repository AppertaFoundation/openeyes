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
    Then I delete the event

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number           |lastName|firstName|      event_id      |
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |      4686470       |
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |      4686471       |
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |      4686472       |
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |      4686473       |
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |      4686474       |
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |      4686475       |
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |      4686476       |
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |      4686477       |
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |      4686478       |
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |      4686479       |



