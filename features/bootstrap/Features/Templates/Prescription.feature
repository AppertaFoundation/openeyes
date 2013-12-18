
Feature: Create New Prescription
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario Outline: Login and create a new Prescription

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

    Then I select a Common Drug "75"
#    And I select a Standard Set of "10"
    Then I enter a Dose of "2" drops
    And I enter a route of "1"

    And I enter a frequency of "4"
    Then I enter a duration of "1"
    Then I enter a eyes option "1"
    #!!Eyes option last to be selected as duration sometimes causes it to be reset!!

#    And I add Prescription comments of "TEST COMMENTS"

    Then I Save the Prescription Draft

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

