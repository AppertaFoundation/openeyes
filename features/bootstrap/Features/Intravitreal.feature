@Intravitreal

Feature: Create New Anaesthetic Satisfaction Audit
  In order to cover every possible route throughout the site
  As an automation tester
  I want to build a template with supporting code for each web page

  Scenario Outline: Login and create a Anaesthetic Satisfaction Audit

    Given I am on the OpenEyes "<environment>" homepage
    And I enter login credentials "<username>" and "<password>"
    And I select Site "<site>"
    Then I select a firm of "1"

    Then I search for patient name last name "<last>" and first name "<first>"

    Then I select Create or View Episodes and Events
    Then I select Add First New Episode and Confirm
    And I add a New Event "<EventType>"

    #Anaesthetic
    Then I choose Right Anaesthetic Type of Topical
    Then I choose Right Anaesthetic Type of LA

    Then I choose Right Anaesthetic Delivery of Retrobulbar
    Then I choose Right Anaesthetic Delivery of Peribulbar
    Then I choose Right Anaesthetic Delivery of Subtenons
    Then I choose Right Anaesthetic Delivery of Subconjunctival
    Then I choose Right Anaesthetic Delivery of Topical
    Then I choose Right Anaesthetic Delivery of TopicalandIntracameral
    Then I choose Right Anaesthetic Delivery of Other
    And I choose Right Anaesthetic Agent "<AnaAgent>"

    #Treatment
    Then I choose Right Pre Injection Antiseptic "1"
    Then I choose Right Pre Injection Skin Cleanser "2"
    And I tick the Right Pre Injection IOP Lowering Drops checkbox

    Then I choose Right Drug "7"
    And I enter "2" number of Right injections
    Then I enter Right batch number "123"
    And I enter a Right batch expiry date of "31"
    Then I choose Right Injection Given By "1"
    And I enter a Right Injection time of "09:30"

    #Anterior Segment
    Then I choose A Right Lens Status of "1"
    And I choose Right Counting Fingers Checked Yes
    And I choose Right Counting Fingers Checked No
    And I choose Right IOP Needs to be Checked Yes
    And I choose Right IOP Needs to be Checked No
    Then I choose Right Post Injection Drops
    And I select Right Complications "5"


  Examples: User details
    | environment   | site      | username | password     | hospnumber   | nhs        | last    | first  | EventType     |AnaAgent |
    | master        | 1         | admin    | admin        | 1009465      | 8821388753 | Coffin, | Violet | Intravitreal  |1        |

#Anaesthetic Agenet
#1 = G Amethocaine
#2 = G Benoxinate
#3>= G Proxymetacaine
#4"= Lignocaine 1%
#5"= Bupivocaine

#Pre Injection Antiseptic
#1 =Iodine 5%
#2 = Chlorhexidine

#Pre Injection Skin Cleanser
#1 =Iodine 10%
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

#Injection Give By (Long list - check ID first)
#1 = Root Enoch

#Lens Status
#1 = Phakic
#2 = Aphakic
#3 = Psuedophakic

#Post Injection Drops
  <option value="1">G. Levofloxacin four times daily for 5 days</option>
  <option value="2">G. Chloramphenicol 0.5% four times daily for 5 days</option>

#Complications
  <option data-description_required="0" data-order="1" value="1">Subconjunctival haemorrhage</option>
  <option data-description_required="0" data-order="2" value="2">Conjunctival damage (e.g. tear)</option>
  <option data-description_required="0" data-order="3" value="3">Corneal abrasion</option>
  <option data-description_required="0" data-order="4" value="4">Lens damage</option>
  <option data-description_required="0" data-order="5" value="5">Retinal damage</option>
  <option data-description_required="1" data-order="6" value="6">Other</option>