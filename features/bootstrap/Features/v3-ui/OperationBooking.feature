@regression
Feature: Operation Booking Test
  @EXAM
  @javascript

  Scenario Outline:
  Route 1: Login and create a operation booking event, select necessary options
  Site :  Kings
  Firm :  MR Clinic (Medical Retina)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Change Firm
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for patient name last name "<lastName>" and first name "<firstName>"

    And I add a New Event "<event>"
    #diagnosis
    Given I select Diagnosis Eyes of "<diagnosis_eye>"
    #add diagnosis
    Then I select a Diagnosis of "<diagnosis>"
    #operation eyes
    Then I select Operation Eyes of "<operation_eye>"
    #operation complexity
    Then I select operation complexity "<complexity>"
    #operation consultant
    Then I select No to Consultant required
    #operation site
    Then I select a Operation Site of "<operation_site>"
    #operation priority
    Then I select a Priority of Urgent
    #operation special equipment
    Given I select special equipment required yes
    #operation special details
    Then I enter special equipment details "<equipment_details>"
    #operation add comments
    Then I add comments of "<operation_comment>"
    #operation add rtt coments
    Given enter RTT comments of "<rtt_comment>"
          #operation procedure
    Then I select a Procedure of "<procedure>"
    #opearation anaesthetic type
    Given I select a Anaesthetic type "<Ana_type>"
    #operation anaesthetic choice
    Then I select Patient preference for Anaesthetic choice
    #operation patient needs
    Given I select No for Patient needs to stop medication
    #operation pre-assessment
    Given I select No for Does the patient require pre-op assessment by an anaesthetist
    #operation overnight stay
    Then I select overnight stay required "<overnight_option>"
    #schedule options
    Then I select schedule option "<schedule_option>"
    #contact collect name
    Then I enter collector name "<name>"
    #contact collect number
    Then I enter collector number "<number>"
    Then I Save the Operation Booking and confirm it saved correctly
    Then I delete the event

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number           |lastName|firstName|event                            |diagnosis_eye|operation_eye|complexity |procedure          |operation_site|equipment_details|operation_comment|rtt_comment|schedule_option|name   |number   |Ana_type|overnight_option|diagnosis|
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |OphTrOperationbooking            |Both         |Left         |High       | Biopsy of choroid |Kings         |testing          |testing testing  |testing    |am             |testing|012345678|LA        |Pre-op        |Amblyopia|
