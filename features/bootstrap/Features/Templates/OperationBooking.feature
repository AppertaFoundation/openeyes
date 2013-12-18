
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
  # !!!THIS TEST WILL FAIL IF THERE ARE OVER 66 EVENTS - THE SAVE BUTTON WILL NOT BE IN VIEW!!!

    Then I change the Viewing Schedule to Emergency List
  # Then I select Next Month

    And I select an Available theatre slot date
    And I select an Available session time
#    Then I add Session comments of "Insert session comments here"
#    And I add Operation comments of "Insert operation comments here"

    Then I confirm the operation slot





  Examples: User details
    | environment   | site      | username | password     | hospnumber   | nhs        | last    | first  | EventType | DiagEyes  | Diagnosis | OpEyes |Procedure | AnaType    |
    | master        | 1         | admin    | admin        | 1009465      | 8821388753 | Coffin, | Violet | OpBooking | Both      | 95717004  | Both   | 41       | Topical    |

  # Firm 1 = Anderson (Cataract)




#Diagnosis Cataract
    
#  30041005">Acute angle-closure glaucoma
#  24010005">Aphakia
#  302905001">Aspergillus otomycosis
#  232000008">Choroidal effusion
#  33647009">Chronic angle-closure glaucoma
#  50485007">Low tension glaucoma
#  4210003">Ocular hypertension
#  46168003">Pigmentary glaucoma
#  392288006">Primary angle-closure glaucoma
#  95217000">Pseudophakia
#  95717004">Secondary glaucoma
#  232064001">Wagner syndrome

  # Procedure Kings

# 48">Anterior capsulotomy
# 41">Anterior vitrectomy
# 47">Capsulectomy
# 61">Capsulotomy (surgical)
# 62">Capsulotomy (YAG)
# 73">Corneal suture adjustment
# 79">Extracapsular cataract extraction
# 323">Extracapsular cataract extraction and insertion of IOL
# 324">Injection into anterior chamber
# 173">Insertion of IOL
# 50">Irrigation of anterior chamber
# 340">Limbal relaxing incision
# 175">Peripheral iridectomy
# 42">Phakoemulsification
# 308">Phakoemulsification and IOL
# 40">Removal of corneal suture
# 46">Removal of IOL
# 52">Repair of prolapsed iris
# 45">Repositioning of IOL
# 322">Revision of IOL
# 53">Surgical iridotomy
# 38">Suture of cornea


# Operation Site ID's:
#1 Queens
#2 Kings

  # For Available theatre and session I have coded to select the first elements which are "Available" - so not to rely on potentially expired dates
  # we can alter this to allow Example Date to be used instead



