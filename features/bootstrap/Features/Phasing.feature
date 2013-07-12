@Phasing
Feature: Create New Phasing Event

  Scenario Outline: Login and create a Phasing Event

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "<site>"
    Then I select a firm of "1"

    Then I search for patient name last name "<last>" and first name "<first>"

    Then I select Create or View Episodes and Events
    Then I select Add First New Episode and Confirm
    And I add a New Event "<EventType>"

    Then I choose a right eye Intraocular Pressure Instrument  of "4"
    And I choose right eye Dilation of "0"
    Then I choose a right eye Intraocular Pressure Reading of "5"
    And I add right eye comments of "Right eye comments here"

    Then I choose a left eye Intraocular Pressure Instrument  of "5"
    And I choose left eye Dilation of "0"
    Then I choose a left eye Intraocular Pressure Reading of "7"
    And I add left eye comments of "Left eye comments here"

    Then I Save the Phasing Event

    #Then I choose to close the browser

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