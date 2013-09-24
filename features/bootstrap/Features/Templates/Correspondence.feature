
Feature: Create New Correspondence
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario Outline: Login and fill in a Correspondence

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "1"
    Then I select a firm of "3"

    Then I search for hospital number "<hospnumber>"
  #Then I search for patient name last name "<last>" and first name "<first>"

  #Then I select Add First New Episode and Confirm
    Then I select Create or View Episodes and Events
  #Then I select the Latest Event
  #Then I expand the Cataract sidebar
    Then I expand the Glaucoma sidebar
    And I add a New Event "<EventType>"

    Then I select Site ID "4"
    And I select Address Target "gp"
    Then I choose a Macro of "site42"

    And I select Clinic Date "7"

#    Then I choose an Introduction of "site21"
#    And I add Findings of "examination334"
#    And I choose a Diagnosis of "site541"
#    Then I choose a Management of "site181"
#    And I choose Drugs "site301"
#    Then I choose Outcome "site341"

    And I choose CC Target "patient"

    And I add a New Enclosure

    Then I Save the Correspondence Draft




  Examples: User details
    | environment   | username | password     | hospnumber   | nhs        | last    | first  | EventType        |
    | master        | admin    | admin        | 1009465      | 8821388753 | Coffin, | Violet | Correspondence   |

  #Environment
  # master, develop

  # Firm 18 = Allan Bruce (Cataract)

  # Site ID's:
  # "1= Moorfields at City Road
  #2"=Moorfields at Bedford Hospital
  #3"=Moorfields at Ealing Hospital
  #4"=Moorfields at Northwick Park Hospital
  #5"=Moorfields at St George's Hospital
  #6"=Moorfields at Mile End Hospital
  #7"=Moorfields at Potters Bar Community Hospital
  #8"=Moorfields at Queen Mary's Hospital, Roehampton
  #9"=Moorfields at St Ann's Hospital, Tottenham
  #10"=Moorfields at Bridge Lane Health Centre, Battersea
  #11"=Moorfields at Boots Opticians, Watford
  #12"=Moorfields at Loxford Polyclinic, Redbridge
  #14"=Moorfields at Teddington Memorial Hospital
  #15"=Moorfields at Upney Lane Health Centre, Barking
  #16"=Moorfields at Visioncare Eye Medical Centre, Wealdstone
  #17"=Moorfields at Mayday Hospital, Croydon
  #18"=Moorfields at Homerton Hospital, Hackney
  #19"=Moorfields at Princess Alexandra Hospital, Harlow
  #20"=Moorfields at Watford General Hospital

  #Target & CC Target =  gp | patient

  #Macro:
  #subspecialty1 =Post-op
  #site22 =Drug change
  #site42 =Annual review
  #site62 =Discharged
  #site82 =Outpatient Visit

  #Introduction:
  #site1 = Refer
  #site21 = Follow up visit
  #site41 = Referral
  #site61 = A&E Walk in

  #Diagnosis:
  #site81 = Principal
  #site541 =Secondary

  #Management:
  #site101 = Benefit
  #site121 = Tail off topical medication
  #site141 = Listed with date
  #site161 = Listed no date
  #site181 = Declined surgery
  #site201 = No treatment
  #site221 = Thinking
  #site241 = Observation

  #Drugs:
  #site261 = Reducing
  #site281 = Prescription
  #site301 = Stopped

  #Outcome:
  #site321 = Take back
  #site341 = Optician
  #site361 = Refer to Cataract
  #site381 = VR follow up
  #site401 = Discharge