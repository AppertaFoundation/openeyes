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


    Then I search for patient name last name "<lastName>" and first name "<firstName>"
    And I add a New Event "<event>"
    Then I select Event Sub Type of "<event_sub_type>"
    Then I click on double files upload
    Then I upload left file "<file_path>"
    Then I upload right file "<file_path>"
    Then I save document event and confirm it saved successfully
    Then I delete the event
    Then I logout

    Examples:
      |uname|pwd  |lastName|firstName|event            | event_sub_type|file_path                                        |
      |admin|admin|Coffin, |Violet   |OphCoDocument    | OCT           |/var/www/openeyes/features/data/assets/index.jpg |
