@operationbooking @regression
Feature: Create New Operation Booking Event
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario Outline: Login and create a Operation Booking

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "2"
    Then I select a firm of "3"

    Then I search for hospital number "<hospnumber>"
    #Then I search for patient name last name "<last>" and first name "<first>"

    Then I select the Latest Event
    #Then I select Create or View Episodes and Events
    Then I expand the Glaucoma sidebar
    And I add a New Event "<EventType>"

    Then I select Diagnosis Eyes of "Left"
    And I select a Diagnosis of "30041005"
    Then I select Operation Eyes of "Left"
    And I select a Procedure of "41"

    #Then I select Yes to Consultant required
    Then I select No to Consultant required

    And I select a Anaesthetic type "<AnaType>"

    Then I select Yes to a Post Operative Stay
    Then I select No to a Post Operative Stay

    And I select a Operation Site of "1"

    #Then I select a Priority of Routine
    Then I select a Priority of Urgent

    And I select a decision date of "14"

#    Then I add comments of "Insert test comments here"

    #Then I select Save and Schedule later

    Then I select Save and Schedule now
    And I select an Available theatre slot date
    And I select an Available session time
#    Then I add Session comments of "Insert session comments here"
#    And I add Operation comments of "Insert operation comments here"

    Then I confirm the operation slot



  Examples: User details
    | environment   | site      | username | password     | hospnumber   | nhs        | last    | first  | EventType | DiagEyes  | Diagnosis | OpEyes |Procedure | AnaType    |
    | master        | 1         | admin    | admin        | 1009465      | 8821388753 | Coffin, | Violet | OpBooking | Both      | 95717004  | Both   | 41       | Topical    |

  # Firm 1 = Anderson (Cataract)

  # Site ID's:
  #1  =City Road
  #2  =Bedford
  #11 =Boots
  #10 =Bridge lane
  #17 =Croydon
  #3  =Ealing
  #19 =Harlow
  #18 =Homerton
  #12 =Loxford
  #6  =Mile End
  #4  =Northwick Park
  #7  =Potters Bar
  #8  =Queen Mary's
  #9  =St Ann's
  #5  =St George's
  #14 =Teddington
  #15 =Upney lane
  #16 =Visioncare
  #20 =Watford

  # Procedure 41 = Anterior vitrectomy

  # Last name to include a comma after to match search criteria i.e Coffin,

  # For Available theatre and session I have coded to select the first elements which are "Available" - so not to rely on potentially expired dates
  # we can alter this to allow Example Date to be used instead



