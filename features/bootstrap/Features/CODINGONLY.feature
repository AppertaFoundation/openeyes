@testo
Feature: Coding only

  Scenario: Route 4: Login and create a new Therapy Application (Queens Site, Anderson Medical Retinal)
  Right Diagnosis: (Coates Disease)
  Treatment: PDT
  NON COMPLIANT

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "admin" and "admin"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "1009465"

    Then I select Create or View Episodes and Events

    Then I expand the Glaucoma sidebar
    And I add a New Event "Therapy"

    And I remove the left side

    And I select a Right Side Diagnosis of "360455002"

    Then I select a Right Treatment of "4"

    And I select a Right Patient has CNV of No

    Then I select Right Cerebrovascular accident Yes

    Then I select Right Ischaemic attack No

    Then I select Right Myocardial infarction Yes

    And I select a Right Consultant of "4"
    And I select an Intended Site of "1"
    And I select "Yes" for "Patient consents to share data"

    Then I select a Right Standard Intervention Exists of Yes

    And I choose a Right Standard Intervention of "4"
    And I select a Right Standard Intervention Previous of No

    Then I select Right Instead of the standard (Deviation)

    And I add Right details of deviation of "Deviation Details Comment box"

    Then I choose a Right reason for not using standard intervention of "2"
    Then I add Right How is the patient different to others of "How is the patient significantly different to others comments?"
    And I add Right How is the patient likely to gain benefit "How is the patient likely to gain more benefit than otherwise comments?"

    Then I select Right Patient Factors No

    And I add Right Patient Expectations of "Patient Expectations comments"

    Then I add Right Anticipated Start Date of "3"

    Then I add Right Clinical Reason for Urgency of "Reason for Urgency"

    Then I Save the Therapy Application and confirm it has been created successfully



