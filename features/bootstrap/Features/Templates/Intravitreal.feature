
Feature: Create New Intravitreal Event
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario Outline: Login and create a Anaesthetic Satisfaction Audit

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "1"
    Then I select a firm of "1"

    Then I search for hospital number "<hospnumber>"
  #Then I search for patient name last name "<last>" and first name "<first>"

  #Then I select Add First New Episode and Confirm
#    Then I select Create or View Episodes and Events
  Then I select the Latest Event
  Then I expand the Cataract sidebar
#    Then I expand the Glaucoma sidebar
    And I add a New Event "<EventType>"

    #Anaesthetic Right
    Then I select Add Right Side
    Then I choose Right Anaesthetic Type of Topical
    Then I choose Right Anaesthetic Type of LA

    Then I choose Right Anaesthetic Delivery of Retrobulbar
    Then I choose Right Anaesthetic Delivery of Peribulbar
    Then I choose Right Anaesthetic Delivery of Subtenons
    Then I choose Right Anaesthetic Delivery of Subconjunctival
    Then I choose Right Anaesthetic Delivery of Topical
    Then I choose Right Anaesthetic Delivery of TopicalandIntracameral
    Then I choose Right Anaesthetic Delivery of Other
    And I choose Right Anaesthetic Agent "5"

    #Anaesthetic Left
    Then I choose Left Anaesthetic Type of Topical
    Then I choose Left Anaesthetic Type of LA

    Then I choose Left Anaesthetic Delivery of Retrobulbar
    Then I choose Left Anaesthetic Delivery of Peribulbar
    Then I choose Left Anaesthetic Delivery of Subtenons
    Then I choose Left Anaesthetic Delivery of Subconjunctival
    Then I choose Left Anaesthetic Delivery of Topical
    Then I choose Left Anaesthetic Delivery of TopicalandIntracameral
    Then I choose Left Anaesthetic Delivery of Other
    And I choose Left Anaesthetic Agent "1"

    #Right Treatment
    Then I choose Right Pre Injection Antiseptic "1"
    Then I choose Right Pre Injection Skin Cleanser "2"
    And I tick the Right Pre Injection IOP Lowering Drops checkbox
    Then I choose Right Pre Injection IOP Lowering Drops "1"
    Then I choose Right Drug "7"
    And I enter "2" number of Right injections
    Then I enter Right batch number "123"
#    And I enter a Right batch expiry date of "31"
    Then I choose Right Injection Given By "1"
    And I enter a Right Injection time of "09:30"

    #Left Treatment
    Then I choose Left Pre Injection Antiseptic "1"
    Then I choose Left Pre Injection Skin Cleanser "2"
    And I tick the Left Pre Injection IOP Lowering Drops checkbox
    Then I choose Left Pre Injection IOP Lowering Drops "1"
    Then I choose Left Drug "7"
    And I enter "2" number of Left injections
    Then I enter Left batch number "123"
#    And I enter a Left batch expiry date of "31"
    Then I choose Left Injection Given By "1"
    And I enter a Left Injection time of "09:30"

    #Right Post Injection Examination
    Then I choose A Right Lens Status of "1"
    And I choose Right Counting Fingers Checked Yes
    And I choose Right Counting Fingers Checked No
    And I choose Right IOP Needs to be Checked Yes
    And I choose Right IOP Needs to be Checked No
    Then I choose Right Post Injection Drops "1"
    
    #Left Post Injection Examination
    Then I choose A Left Lens Status of "1"
    And I choose Left Counting Fingers Checked Yes
    And I choose Left Counting Fingers Checked No
    And I choose Left IOP Needs to be Checked Yes
    And I choose Left IOP Needs to be Checked No
    Then I choose Left Post Injection Drops "1"
    
    #Complications
    And I select Right Complications "5"
    And I select Left Complications "5"

    Then I Save the Intravitreal injection



  Examples: User details
    | environment   | site      | username | password     | hospnumber   | nhs        | last    | first  | EventType     |
    | master        | 1         | admin    | admin        | 1009465      | 8821388753 | Coffin, | Violet | Intravitreal  |

#Anaesthetic Agenet
#1 = G Amethocaine
#2 = G Benoxinate
#3 = G Proxymetacaine
#4 = Lignocaine 1%
#5 = Bupivocaine

#Pre Injection Antiseptic
#1 = Iodine 5%
#2 = Chlorhexidine

#Pre Injection Skin Cleanser
#1 = Iodine 10%
#2 = Chlorhexidine

#Drug    
#1">Avastin
#2">Eylea
#3">Lucentis
#4">Macugen
#5">PDT
#6">Ozurdex
#7">Intravitreal triamcinolone
#8">Illuvien

#Injection Given By (Long list - check ID first)
#1 = Root Enoch

#Lens Status
#1 = Phakic
#2 = Aphakic
#3 = Psuedophakic

#Post Injection Drops
#1 = G. Levofloxacin four times daily for 5 days
#2 = G. Chloramphenicol 0.5% four times daily for 5 days

#Complications
#1 = Subconjunctival haemorrhage
#2 = Conjunctival damage (e.g. tear)
#3 = Corneal abrasion
#4 = Lens damage
#5 = Retinal damage
#6 = Other