@therapy @regression
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
    And I select a Right Angiogram Baseline Date of "1"

    Then I select a Left Treatment of "1"
    And I select a Left Angiogram Baseline Date of "1"

    Then I select Cerebrovascular accident Yes
    Then I select Cerebrovascular accident No
    Then I select Ischaemic attack Yes
    Then I select Ischaemic attack No
    Then I select Myocardial infarction Yes
    Then I select Myocardial infarction No

    And I select a Consultant of "4"

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
