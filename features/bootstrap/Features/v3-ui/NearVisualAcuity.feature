@examination @regression
Feature: Near Visual Acuity test
  @EXAM
  @javascript

  Scenario Outline:
  Route 1: Login and create a near visual acuity:
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
    And I add Near Visual Acuity
    Then I select a "left" Near Visual Acuity of "60" using "1"
    Then I select a "right" Near Visual Acuity of "60" using "1"

    Then I Save the Event and confirm it has been created successfully
    Then I delete the event
    Then I logout

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number           |lastName|firstName|event            |
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |OphCiExamination |