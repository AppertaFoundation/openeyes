
Feature: Create New Operation Booking Event
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario Outline: EROD Test Script

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "<hospnumber>"

    Then I select the Latest Event
  #Then I select Create or View Episodes and Events
    Then I expand the Glaucoma sidebar
    And I add a New Event "<EventType>"

    Then I select Diagnosis Eyes of "Left"
    And I select a Diagnosis of "30041005"
    Then I select Operation Eyes of "Left"
    And I select a Procedure of "41"

    Then I select No to Consultant required

    And I select a Anaesthetic type "<AnaType>"

    Then I select No to a Post Operative Stay

    And I select a Operation Site of "1"

    Then I select a Priority of Routine

    And I select a decision date of "14"

    Then I select Save and Schedule now

    Then I select an Available theatre slot date three weeks in the future
    And I select an Available session time with No Anaesthetist

  Examples: User details
    | environment   | site      | username | password     | hospnumber   | nhs        | last    | first  | EventType | DiagEyes  | Diagnosis | OpEyes |Procedure | AnaType    |
    | master        | 1         | admin    | admin        | 1009465      | 8821388753 | Coffin, | Violet | OpBooking | Both      | 95717004  | Both   | 41       | Topical    |

# This feature uses OperationBooking.php and OperationBookingContext.php