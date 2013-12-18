@code
Feature: Create New Anaesthetic Satisfaction Audit
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario Outline: Login and create a new Therapy Application

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "2"
    Then I select a firm of "4"

    Then I search for hospital number "<hospnumber>"
  #    Then I search for patient name last name "<last>" and first name "<first>"

    Then I select the Latest Event
  #Then I select Create or View Episodes and Events
  #Then I select Add First New Episode and Confirm

    Then I expand the Medical Retinal sidebar
    And I add a New Event "<EventType>"

#    Then I add Right Side

    And I select a Right Side Diagnosis of "75971007"
    Then I select a Right Secondary To of "267718000"

    And I select a Left Side Diagnosis of "75971007"
    Then I select a Left Secondary To of "267718000"

    Then I select a Right Treatment of "1"
#    And I select a Right Angiogram Baseline Date of "1"

    Then I select a Left Treatment of "1"
#    And I select a Left Angiogram Baseline Date of "1"

  # NOTES: The Angiogram Baseline Dates occasionally fail with the datepicker and a solution so far has not been found

    Then I select Right Cerebrovascular accident Yes
    Then I select Right Cerebrovascular accident No
    Then I select Right Ischaemic attack Yes
    Then I select Right Ischaemic attack No
    Then I select Right Myocardial infarction Yes
    Then I select Right Myocardial infarction No

    And I select a Right Consultant of "4"

    Then I select a Right Standard Intervention Exists of Yes
#    Then I select a Right Standard Intervention Exists of No
    And I choose a Right Standard Intervention of "1"
    And I select a Right Standard Intervention Previous of Yes
#    And I select a Right Standard Intervention Previous of No
#    Then I select Right In addition to the standard (Additional)
    Then I select Right Instead of the standard (Deviation)

#    And I add Right details of additional of "Additional Details Comment box"
    And I add Right details of deviation of "Deviation Details Comment box"

    Then I choose a Right reason for not using standard intervention of "1"
    Then I add Right How is the patient different to others of "How is the patient significantly different to others comments?"
    And I add Right How is the patient likely to gain benefit "How is the patient likely to gain more benefit than otherwise comments?"

    Then I select Right Patient Factors Yes
#    Then I select Right Patient Factors No

    Then I add Right Patient Factor Details of "Patient Factor Details comments"
    And I add Right Patient Expectations of "Patient Expectations comments"

    Then I add Right Anticipated Start Date of "5"

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


  Examples: User details
    | environment   | site      | username | password     | hospnumber   | nhs        | last    | first  | EventType     |
    | master        | 1         | admin    | admin        | 1009465      | 8821388753 | Coffin, | Violet | Therapy       |

# Therapy Application requires a firm of Medical Retinal to enable a consultant to be assigned
# The I add Right Side - this sometimes seems to be open by default (Medical Retinal forced??)

#Diagnosis
# 312956001 = Central serous chorioretinopathy
# 313001006 = Idiopathic polypoidal choroidal vasculopathy
# 360455002 = Coats' disease
# 128473001 = Uveitis
# 78370002 = Scleritis

#Secondary To
# 4855003 Diabetic retinopathy
# 24596005 Venous retinal branch occlusion
# 68478007 Central retinal vein occlusion
# 232024000 Parafoveal telangiectasia
# 276436007 Hereditary macular dystrophy

#Consultant
# 4 = Anderson Firm
# 29 = Bessant David
