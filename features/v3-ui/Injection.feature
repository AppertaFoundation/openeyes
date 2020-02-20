 @regression
Feature: Intravitreal Injection Test
  @EXAM
  @javascript

  Scenario Outline:
  Route 1: Login and create a injection event, select all necessary right and left options and create the event, then delete it.
  Site :  Kings
  Firm :  MR Clinic (Medical Retina)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"


    Then I search for patient name last name "<lastName>" and first name "<firstName>"
    And I select the Medical Retina option in the sidebar

    And I add a New Event "<event>"
    Then I select Add Right Side
    Then I choose Right Anaesthetic Delivery of Topical
    Then I choose Right Anaesthetic Agent "<right_agent>"
    Then I choose Right Pre Injection Antiseptic "<right_pre_antiseptic>"
    Then I choose Right Pre Injection Skin Cleanser "<right_pre_skin>"
    Then I choose Right Drug "<right_drug>"
    Then I enter Right batch number "<right_batch_number>"
    Then I choose Right Injection Given By "<right_injection_by>"
    Then I choose Right Counting Fingers Checked Yes
    Then I choose Right IOP Needs to be Checked Yes
    Then I choose Right Post Injection Drops "<right_post_drop>"
    #Then I add Right Complications Comments of "<right_comment>"

    Then I select Add Left Side
    Then I choose Left Anaesthetic Delivery of Topical
    Then I choose Left Anaesthetic Agent "<left_agent>"
    Then I choose Left Pre Injection Antiseptic "<left_pre_antiseptic>"
    Then I choose Left Pre Injection Skin Cleanser "<left_pre_skin>"
    Then I choose Left Drug "<left_drug>"
    Then I enter Left batch number "<left_batch_number>"
    Then I choose Left Injection Given By "<left_injection_by>"
    Then I choose Left Counting Fingers Checked Yes
    Then I choose Left IOP Needs to be Checked Yes
    Then I choose Left Post Injection Drops "<left_post_drop>"
    #Then I add Left Complications Comments of "<left_comment>"

    Then I Save the Intravitreal injection and confirm it has been created successfully
    Then I delete the event
    Then I logout

    Examples:
      |uname|pwd  |lastName|firstName|event                            |right_agent   |right_pre_antiseptic |right_pre_skin |right_drug |right_batch_number |right_injection_by |right_post_drop|right_comment|left_agent   |left_pre_antiseptic |left_pre_skin |left_drug                   |left_batch_number |left_injection_by |left_post_drop                               |left_comment     |
      |admin|admin|Coffin, |Violet   |OphTrIntravitrealinjection       |G Benoxinate  |Iodine 5%            |Iodine 10%     |Foscarnet  |3                  |Stevens Simon      |None           |Lens damage  |G Benoxinate |Iodine 5%           |Iodine 10%    |Photodynamic Therapy (PDT)  |5                 |Stevens Simon     | G. Levofloxacin four times daily for 5 days |Corneal abrasion |
