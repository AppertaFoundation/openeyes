
Feature: Create New Phasing Event
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario Outline: Login and create a Phasing Event

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "<hospnumber>"
  #Then I search for patient name last name "<last>" and first name "<first>"

  #Then I select Add First New Episode and Confirm
#    Then I select Create or View Episodes and Events
  Then I select the Latest Event
  #Then I expand the Cataract sidebar
    Then I expand the Glaucoma sidebar
    And I add a New Event "<EventType>"


    Then I choose a right eye Intraocular Pressure Instrument  of "1"
    Then I choose a right eye Intraocular Pressure Instrument  of "2"
    Then I choose a right eye Intraocular Pressure Instrument  of "3"
    Then I choose a right eye Intraocular Pressure Instrument  of "5"
    Then I choose a right eye Intraocular Pressure Instrument  of "4"
    And I choose right eye Dilation of Yes
#    And I choose right eye Dilation of No
    Then I choose a right eye Intraocular Pressure Reading Time of "14:00"
    Then I choose a right eye Intraocular Pressure Reading of "5"
    And I add right eye comments of "Right eye comments here"

    Then I choose a left eye Intraocular Pressure Instrument  of "1"
    Then I choose a right eye Intraocular Pressure Instrument  of "2"
    Then I choose a right eye Intraocular Pressure Instrument  of "3"
    Then I choose a right eye Intraocular Pressure Instrument  of "5"
    Then I choose a right eye Intraocular Pressure Instrument  of "4"

    And I choose left eye Dilation of Yes
#    And I choose left eye Dilation of No
    Then I choose a left eye Intraocular Pressure Reading Time of "14:42"
    Then I choose a left eye Intraocular Pressure Reading of "7"
    And I add left eye comments of "Left eye comments here"

    Then I Save the Phasing Event

  Examples: User details
    | environment   | site      | username | password     | hospnumber   | nhs        | last    | first  | EventType     |
    | master        | 1         | admin    | admin        | 1009465      | 8821388753 | Coffin, | Violet | Phasing       |


# Site ID's:
# City Road - 1
# Last name to include a comma after to match search criteria i.e Coffin,
# Anaesthetist - non = Non-Consultant, no = No Consultant

# Firm 18 = Allan Bruce (Cataract)

# 1 = Goldmann
# 2 = Tono-pen
# 3 = I-care
# 4 = Perkins
# 5 = Other

# Dilated 1=Yes 0=No