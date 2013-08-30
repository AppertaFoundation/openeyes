@therapy
Feature: Create New Anaesthetic Satisfaction Audit
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario Outline: Login and create a Anaesthetic Satisfaction Audit

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "<site>"
    Then I select a firm of "1"

    Then I search for patient name last name "<last>" and first name "<first>"

    Then I select Create or View Episodes and Events
    Then I select Add First New Episode and Confirm
    And I add a New Event "<EventType>"

    Then I add Right Side
    And I select a Right Side Diagnosis of "78370002"
    And I select a Left Side Diagnosis of "312956001"
    Then I select a Right Secondary To of "4855003"
    Then I select a Left Secondary To of "360455002"

    Then I select Cerebrovascular accident Yes
    Then I select Cerebrovascular accident No
    Then I select Ischaemic attack Yes
    Then I select Ischaemic attack No
    Then I select Myocardial infarction Yes
    Then I select Myocardial infarction No

    And I select a Consultant of "29"

    Then I Save the Therapy Application


  Examples: User details
    | environment   | site      | username | password     | hospnumber   | nhs        | last    | first  | EventType     |
    | master        | 1         | admin    | admin        | 1009465      | 8821388753 | Coffin, | Violet | Laser         |



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
# 106 = Andrews Richard
# 29 = Bessant David
# 77 = daCruz Lyndon
# 95 = Desai Parul