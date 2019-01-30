@document @regression
Feature: Document test
  @EXAM
  @javascript

  Scenario Outline:
  Route 1: Login and create a Document event, check the other option appears after already being selected
  Site :  Kings
  Firm :  MR Clinic (Medical Retina)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Change Firm
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for patient name last name "<lastName>" and first name "<firstName>"
    And I add a New Event "<event>"
    Then I select Event Sub Type of "<event_sub_type>"
    #Then I click on double files upload
    #Then I click on double document upload left
    Then I upload single file "<file_path>"
    Then I save document event and confirm it saved successfully
    Then I delete the event

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number           |lastName|firstName|event            | event_sub_type|file_path                     |
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |OphCoDocument    | OCT           |/var/www/openeyes/assets/index.jpg|