@regression
Feature: Laser test
  @EXAM
  @javascript

  Scenario Outline:
  Route 1: Login and create a Laser event, select several options and save the event, then delete it
  Site :  Kings
  Firm :  MR Clinic (Medical Retina)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Change Firm
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for patient name last name "<lastName>" and first name "<firstName>"

    And I add a New Event "<event>"
    Then I select a Laser site ID "<Laser_site>"
    Given I select a Laser of "<Laser>"
    Given I select a Laser Operator of "<Laser_operator>"
    Then I select a Left Procedure of "<Procedure>"
    Then I select a Right Procedure of "<Procedure>"


    Then I save the Laser Event and confirm it has been created successfully
    Then I delete the event
    Then I logout

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number           |lastName|firstName|event            | Laser_site | Laser| Laser_operator| Procedure| Procedure_id| New_Procedure|
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |OphTrLaser       | Kings      | Yag  | Jones George  |Laser gonioplasty|364|Cycloablation    |
