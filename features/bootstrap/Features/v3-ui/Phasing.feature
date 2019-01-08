@regression
Feature: Phasing Test
  @EXAM
  @javascript

  Scenario Outline:
  Route 1: Login and create a Phasing event, check the other option appears after already being selected
  Site :  Kings
  Firm :  MR Clinic (Medical Retina)

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Change Firm
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for patient name last name "<lastName>" and first name "<firstName>"

    And I add a New Event "<event>"

    Then I choose a right eye Intraocular Pressure Instrument  of "<right_instrument>"
    Then I choose right eye Dilation of Yes
    Then I choose a right eye Intraocular Pressure Reading Time of "<right_reading_time>"
    Then I choose a right eye Intraocular Pressure Reading of "<right_reading>"
    Then I add right eye comments of "<right_comment>"

    Then I choose a left eye Intraocular Pressure Instrument  of "<left_instrument>"
    Then I choose left eye Dilation of Yes
    Then I choose a left eye Intraocular Pressure Reading Time of "<left_reading_time>"
    Then I choose a left eye Intraocular Pressure Reading of "<left_reading>"
    Then I add left eye comments of "<left_comment>"

    Then I Save the Phasing Event and confirm it has been created successfully





    Examples:
      |uname|pwd  |siteName/Number|firmName/Number           |lastName|firstName|event                  |right_instrument   |right_reading_time |right_reading |right_comment       |left_instrument   |left_reading_time |left_reading |left_comment|
      |admin|admin|Kings          |MR Clinic (Medical Retina)|Coffin, |Violet   |OphCiPhasing           |Goldmann           |01:13              |128           |Nothing to comment  |Other             |05:06             |23           |Seriously   |
