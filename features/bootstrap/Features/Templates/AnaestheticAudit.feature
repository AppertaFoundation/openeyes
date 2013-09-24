
Feature: Create New Anaesthetic Satisfaction Audit
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario Outline: Login and create a Anaesthetic Satisfaction Audit

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "<hospnumber>"
#    Then I search for patient name last name "<last>" and first name "<first>"

    Then I select the Latest Event
    #Then I select Create or View Episodes and Events
    #Then I select Add First New Episode and Confirm
    #Then I expand the Cataract sidebar
    Then I expand the Glaucoma sidebar
    And I add a New Event "<EventType>"

    Then I select an Anaesthetist "<Anaesthetist>"
    And I select Satisfaction levels of Pain "<pain>" Nausea "<nausea>"

    And I tick the Vomited checkbox
    And I untick the Vomited checkbox

    Then I select Vital Signs of Respiratory Rate "<resprate>" Oxygen Saturation "<oxysat>" Systolic Blood Pressure "<sysblood>"
    And I select Vital Signs of Body Temperature "<temp>" and Heart Rate "<heart>" Conscious Level AVPU "<AVPU>"

    Then I enter Comments "Here are some test comments"

    And I select the Yes option for Ready to Discharge
    And I select the No option for Ready to Discharge

    Then I Save the Event
    #Then I Cancel the Event

  Examples: User details
    | environment   | site      | username | password     | hospnumber   | nhs        | last            | first  | EventType     | Anaesthetist | pain | nausea | resprate | oxysat | sysblood | temp | heart | AVPU |
    | master        | 1         | admin    | admin        | 1009465      | 8821388753 | Coffin, Violet  | Violet | Satisfaction  | no           | 2    | 3      | 3        |  3     |  4       |  5   | 2     | 2    |


  # Site ID's:
  # City Road - 1
  # Last name to include a comma after to match search criteria i.e Coffin,
  # Anaesthetist - non = Non-Consultant, no = No Consultant

  # Event Type
  # Satisfaction = Anaesthetic Satisfaction Audit


  # Respiratory Rate
  #"1" = 8 or less
  #"2" = 9-11
  #"3" = 12-20
  #"4" = 21-24
  #"5" = 25 or above

  # Oxygen Saturation
  #"1" = 85 or lower
  #"2" = 85-89
  #"3" = 90-94
  #"4" = 95 or above

  # Systolic Blood Pressure
  #"1">70 or lower
  #"2">71-80
  #"3">81-95
  #"4">96-189
  #"5">190-199
  #"6">200 or above

  # Body Temperature
  #"1">35 or lower
  #"2">35.1-36
  #"3">36.1-37.4
  #"4">37.5-38.4
  #"5">38.5-38.9
  #"6">39 or above

  # Heart Rate
  #"1">40 or lower
  #"2">41-50
  #"3">51-100
  #"4">101-110
  #"5">111-129
  #"6">130 or above

  # Conscious Level AVPU
  #"2">Alert
  #"3">Responds to VERBAL commands
  #"4">Responds to PAIN
  #"5">Unresponsive