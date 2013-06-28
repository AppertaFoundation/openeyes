@Prescription
Feature: Create New Prescription

  Scenario Outline: Login and create a new Prescription

    Given I am on the OpenEyes "<environment>" homepage
    And I select Site "<site>"
    And I enter login credentials "<username>" and "<password>"

    Then I select a firm of "18"

    #Then I search for hospital number "<hospnumber>"
    Then I search for patient name last name "<last>" and first name "<first>"
    #Then I search for NHS number "<nhs>"

    Then I select Create or View Episodes and Events
    And I add a New Event "<EventType>"

    Then I select a Common Drug "610"
    And I select a Standard Set of "7"
    Then I enter a Dose of "2" drops
    And I enter a route of "1"
    Then I enter a eyes option "3"
    And I enter a frequency of "4"
    Then I enter a duration of "4"
    And I add Prescription comments of "Prescription comments can go here"

    Then I Save the Event

    Then I choose to close the browser

  Examples: User details
    | environment   | site      | username | password     | hospnumber   | nhs        | last    | first  | EventType  |
    | master        | 1         | admin    | admin        | 1009465      | 8821388753 | Coffin, | Violet | Prescription |


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

# Common Drug - 610 = MAXITROL eye drops

# Standard Set - 7 = Post Op

# Route - 1 = Eye
# Eyes Option 3 = Both
# Frequency 4 = 2 Hourly
# Duration 4 = 3 days (yes really...)

