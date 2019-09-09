@regression
Feature: Prescription Test
  @EXAM
  @javascript

  Scenario Outline:
  Route 1: Login and create a prescription event, add drug and usage options and save the event, then delete it
  Site :  Kings
  Firm :  MR Clinic (Medical Retina)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"


    Then I search for patient name last name "<lastName>" and first name "<firstName>"
    And I add a New Event "<event>"

    Then I add drug "<drug>"
    Then I confirm drug added

    Then I enter a Dose of "<dose>" drops
    Given I enter a route of "<route>"
    Then I enter a eyes option "<eye_option>"
    Given I enter a frequency of "<frequency>"
    Then I enter a duration of "<duration>"
    Given I select dispense condition "<condition>"
    Then I select despense location "<location>"

    Given I add Prescription comments of "<comment>"

    Then I Save the Prescription Draft and confirm it has been created successfully
    Then I delete the event
    Then I logout




    Examples:
      |uname|pwd  |lastName|firstName|event                            |drug                                         |comment                |dose|route|eye_option|frequency|duration|condition         |location|
      |admin|admin|Coffin, |Violet   |OphDrPrescription                |heparin 5,000 units in 1mL eye drops         |testing testing testing|10  |Eye  |Both      |6/day    |7 days  |Hospital to supply|Pharmacy|
