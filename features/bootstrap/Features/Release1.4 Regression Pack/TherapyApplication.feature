@therapy @regression
Feature: Create New Therapy Application Event
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

    Scenario: Route 1: Login and create a new Therapy Application (Queens Site, Glaucoma Firm)
    Diagnosis: (Choroidal Retinal Neo) Secondary To: (Age related macular degeneration)
    Treatment: Avastin
    NICE NON-COMPLIANT

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "1"
    Then I select a firm of "2"

    Then I search for hospital number "1009465"

    Then I select the Latest Event

    Then I expand the Glaucoma sidebar
    And I add a New Event "Therapy"

    And I select a Right Side Diagnosis of "75971007"
    Then I select a Right Secondary To of "267718000"

    Then I select a Right Treatment of "1"

    Then I select a Left Treatment of "1"

    Then I select Right Cerebrovascular accident Yes

    Then I select Right Ischaemic attack Yes

    Then I select Right Myocardial infarction Yes

    And I select a Right Consultant of "4"

    Then I select a Right Standard Intervention Exists of Yes

    And I choose a Right Standard Intervention of "1"
    And I select a Right Standard Intervention Previous of Yes

    Then I select Right Instead of the standard (Deviation)

    And I add Right details of deviation of "Deviation Details Comment box"

    Then I choose a Right reason for not using standard intervention of "1"
    Then I add Right How is the patient different to others of "How is the patient significantly different to others comments?"
    And I add Right How is the patient likely to gain benefit "How is the patient likely to gain more benefit than otherwise comments?"

    Then I select Right Patient Factors Yes

    Then I add Right Patient Factor Details of "Patient Factor Details comments"
    And I add Right Patient Expectations of "Patient Expectations comments"

    Then I add Right Anticipated Start Date of "5"

    And I select a Left Side Diagnosis of "75971007"
    Then I select a Left Secondary To of "267718000"

    Then I select a Left Treatment of "1"

    Then I select a Left Standard Intervention Exists of Yes

    And I choose a Left Standard Intervention of "1"
    And I select a Left Standard Intervention Previous of Yes

    Then I select Left Instead of the standard (Deviation)

    And I add Left details of deviation of "Deviation Details Comment box"

    Then I choose a Left reason for not using standard intervention of "1"
    Then I add Left How is the patient different to others of "How is the patient significantly different to others comments?"
    And I add Left How is the patient likely to gain benefit "How is the patient likely to gain more benefit than otherwise comments?"

    Then I select Left Patient Factors Yes

    Then I add Left Patient Factor Details of "Patient Factor Details comments"
    And I add Left Patient Expectations of "Patient Expectations comments"

    Then I add Left Anticipated Start Date of "5"
      
    Then I Save the Therapy Application

