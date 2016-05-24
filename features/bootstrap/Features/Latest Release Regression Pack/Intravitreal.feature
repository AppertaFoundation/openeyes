@intravitreal @regression
Feature: Create New Intravitreal Event
@Intravitreal
         Regression coverage of this event is approx 75%

  @Intravitreal_Route_0
  Scenario Outline: Route 0:  Error tests : Login and create a New Intravitreal Event
            Site 1:  Queens
            Firm 1:  Anderson Cataract
            Mandatory fields - validation error tests

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Add Right Side
    Then I select Add Left Side

    Then I Save the Intravitreal injection

    Then I Confirm that Intravitreal Mandatory fields validation error messages are displayed

    Examples:
    |uname|pwd  |siteName/Number|firmName/Number         |hospNumber|speciality|event                 |
    |admin|admin|Croydon        |Cataract firm (Cataract)|1009465   |Cataract  |Intravitreal injection|


  @Intravitreal_Route_1
  Scenario Outline: Route 1: Login and create a New Intravitreal Event
            Site 1:  Queens
            Firm 1:  Anderson Cataract

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

#    Then I remove the Right Side
    Then I select Add Right Side
    Then I choose Right Anaesthetic Type of Topical
    Then I choose Right Anaesthetic Type of LA

    Then I choose Right Anaesthetic Delivery of Retrobulbar

    And I choose Right Anaesthetic Agent "5"

    Then I select Add Left Side
    Then I choose Left Anaesthetic Type of Topical
    Then I choose Left Anaesthetic Type of LA

    Then I choose Left Anaesthetic Delivery of Retrobulbar

    And I choose Left Anaesthetic Agent "1"

    Then I choose Right Pre Injection Antiseptic "1"
    Then I choose Right Pre Injection Skin Cleanser "2"
    And I tick the Right Pre Injection IOP Lowering Drops checkbox
    Then I choose Right Pre Injection IOP Lowering Drops "1"
    Then I choose Right Drug "7"
    And I enter "2" number of Right injections
    Then I enter Right batch number "123"

    Then I choose Right Injection Given By "11"
    And I enter a Right Injection time of "09:30"

    Then I choose Left Pre Injection Antiseptic "1"
    Then I choose Left Pre Injection Skin Cleanser "2"
    And I tick the Left Pre Injection IOP Lowering Drops checkbox
    Then I choose Left Pre Injection IOP Lowering Drops "1"
    Then I choose Left Drug "7"
    And I enter "2" number of Left injections
    Then I enter Left batch number "123"

    Then I choose Left Injection Given By "11"
    And I enter a Left Injection time of "09:30"

    Then I choose A Right Lens Status of "1"
    And I choose Right Counting Fingers Checked Yes

    And I choose Right IOP Needs to be Checked No
    Then I choose Right Post Injection Drops "1"

    Then I choose A Left Lens Status of "1"
    And I choose Left Counting Fingers Checked Yes


    And I choose Left IOP Needs to be Checked No
    Then I choose Left Post Injection Drops "1"

    And I select Right Complications "5"
    And I select Left Complications "5"

    Then I Save the Intravitreal injection and confirm it has been created successfully

  Examples:
  |uname|pwd  |siteName/Number|firmName/Number         |hospNumber|speciality|event                 |
  |admin|admin|Croydon        |Cataract firm (Cataract)|1009465   |Cataract  |Intravitreal injection|

  @Intravitreal_Route_2
  Scenario Outline: Route 2: Login and create a New Intravitreal Event
            Site 2:  Kings
            Firm 2:  Broom Glaucoma

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Add Right Side
    Then I choose Right Anaesthetic Type of Topical
    Then I choose Right Anaesthetic Type of LA

    Then I choose Right Anaesthetic Delivery of Peribulbar

    And I choose Right Anaesthetic Agent "3"

    Then I choose Left Anaesthetic Type of LA

    Then I choose Left Anaesthetic Delivery of Peribulbar

    And I choose Left Anaesthetic Agent "2"

    Then I choose Right Pre Injection Antiseptic "2"
    Then I choose Right Pre Injection Skin Cleanser "1"
    And I tick the Right Pre Injection IOP Lowering Drops checkbox
    Then I choose Right Pre Injection IOP Lowering Drops "3"
    Then I choose Right Drug "7"
    And I enter "2" number of Right injections
    Then I enter Right batch number "567"

    Then I choose Right Injection Given By "11"
    And I enter a Right Injection time of "09:30"

    Then I choose Left Pre Injection Antiseptic "2"
    Then I choose Left Pre Injection Skin Cleanser "1"
    And I tick the Left Pre Injection IOP Lowering Drops checkbox
    Then I choose Left Pre Injection IOP Lowering Drops "3"
    Then I choose Left Drug "4"
    And I enter "1" number of Left injections
    Then I enter Left batch number "456"

    Then I choose Left Injection Given By "11"
    And I enter a Left Injection time of "09:30"

    Then I choose A Right Lens Status of "1"

    And I choose Right Counting Fingers Checked No
    And I choose Right IOP Needs to be Checked Yes

    Then I choose Right Post Injection Drops "1"

    Then I choose A Left Lens Status of "1"

    And I choose Left Counting Fingers Checked No
    And I choose Left IOP Needs to be Checked Yes

    Then I choose Left Post Injection Drops "1"

    And I select Right Complications "3"
    And I select Left Complications "1"

    Then I Save the Intravitreal injection and confirm it has been created successfully

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number        |hospNumber|speciality|event                 |
      |admin|admin|Croydon        |A K Hamilton (Glaucoma)|1009465   |Glaucoma  |Intravitreal injection|

  
  @Intravitreal_Route_3
  Scenario Outline: Route 3: Login and create a New Intravitreal Event
            Site 1:  Queens
            Firm 1:  Anderson Cataract

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"
    
    Then I select Add Right Side

    Then I choose Right Anaesthetic Type of LA

    Then I choose Right Anaesthetic Delivery of Subtenons

    And I choose Right Anaesthetic Agent "5"

    Then I select Add Left Side
    Then I choose Left Anaesthetic Type of LA

    Then I choose Left Anaesthetic Delivery of Subtenons

    And I choose Left Anaesthetic Agent "4"

    Then I choose Right Pre Injection Antiseptic "1"
    Then I choose Right Pre Injection Skin Cleanser "1"

    Then I choose Right Drug "7"
    And I enter "2" number of Right injections
    Then I enter Right batch number "567"

    Then I choose Right Injection Given By "11"
    And I enter a Right Injection time of "09:30"

    Then I choose Left Pre Injection Antiseptic "1"
    Then I choose Left Pre Injection Skin Cleanser "2"

    Then I choose Left Drug "2"
    And I enter "1" number of Left injections
    Then I enter Left batch number "789"

    Then I choose Left Injection Given By "11"
    And I enter a Left Injection time of "09:30"

    Then I choose A Right Lens Status of "1"

    And I choose Right Counting Fingers Checked No
    And I choose Right IOP Needs to be Checked Yes

    Then I choose Right Post Injection Drops "1"

    Then I choose A Left Lens Status of "2"

    And I choose Left Counting Fingers Checked Yes
    And I choose Left IOP Needs to be Checked No

    Then I choose Left Post Injection Drops "2"

    And I select Right Complications "2"
    And I select Left Complications "2"

    Then I Save the Intravitreal injection and confirm it has been created successfully

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number         |hospNumber|speciality|event                 |
      |admin|admin|Croydon        |Cataract firm (Cataract)|1009465   |Cataract  |Intravitreal injection|


  @Intravitreal_Route_4
  Scenario Outline: Route 4: Login and create a New Intravitreal Event
            Site 1:  Queens
            Firm 4:  Anderson Medical Retinal

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

    Then I select Add Right Side
    Then I choose Right Anaesthetic Type of Topical

    Then I choose Right Anaesthetic Delivery of Subconjunctival

    And I choose Right Anaesthetic Agent "2"

    Then I choose Left Anaesthetic Type of LA

    Then I choose Left Anaesthetic Delivery of Subconjunctival

    And I choose Left Anaesthetic Agent "1"

    Then I choose Right Pre Injection Antiseptic "2"
    Then I choose Right Pre Injection Skin Cleanser "2"

    Then I choose Right Drug "4"
    And I enter "4" number of Right injections
    Then I enter Right batch number "890"

    Then I choose Right Injection Given By "11"
    And I enter a Right Injection time of "11:00"

    Then I choose Left Pre Injection Antiseptic "2"
    Then I choose Left Pre Injection Skin Cleanser "1"

    Then I choose Left Drug "1"
    And I enter "3" number of Left injections
    Then I enter Left batch number "890"

    Then I choose Left Injection Given By "11"
    And I enter a Left Injection time of "08:30"

    Then I choose A Right Lens Status of "2"

    And I choose Right Counting Fingers Checked Yes
    And I choose Right IOP Needs to be Checked No

    Then I choose Right Post Injection Drops "2"

    Then I choose A Left Lens Status of "1"

    And I choose Left Counting Fingers Checked No
    And I choose Left IOP Needs to be Checked Yes

    Then I choose Left Post Injection Drops "1"

    And I select Right Complications "3"
    And I select Left Complications "3"

    Then I Save the Intravitreal injection

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number                |hospNumber|speciality     |event                 |
      |admin|admin|Croydon        |Angela Glasby (Medical Retinal)|1009465   |Medical Retinal|Intravitreal injection|

  @Intravitreal_Route_5
  Scenario Outline: Route 5: Login and create a New Intravitreal Event
  Site 1:  Queens
  Firm 1:  Anderson Cataract
  Other complication and comment fields

    Given I am on the OpenEyes "master" homepage
    And I enter login credentials "<uname>" and "<pwd>"
    And I select Site "<siteName/Number>"
    Then I select a firm of "<firmName/Number>"

    Then I search for hospital number "<hospNumber>"

    Then I select Create or View Episodes and Events

    Then I expand the "<speciality>" sidebar
    And I add a New Event "<event>"

  #    Then I remove the Right Side
    Then I select Add Right Side
    Then I choose Right Anaesthetic Type of Topical
    Then I choose Right Anaesthetic Type of LA

    Then I choose Right Anaesthetic Delivery of Retrobulbar

    And I choose Right Anaesthetic Agent "5"

    Then I select Add Left Side
    Then I choose Left Anaesthetic Type of Topical
    Then I choose Left Anaesthetic Type of LA

    Then I choose Left Anaesthetic Delivery of Retrobulbar

    And I choose Left Anaesthetic Agent "1"

    Then I choose Right Pre Injection Antiseptic "1"
    Then I choose Right Pre Injection Skin Cleanser "2"
    And I tick the Right Pre Injection IOP Lowering Drops checkbox
    Then I choose Right Pre Injection IOP Lowering Drops "1"
    Then I choose Right Drug "7"
    And I enter "2" number of Right injections
    Then I enter Right batch number "123"

    Then I choose Right Injection Given By "11"
    And I enter a Right Injection time of "09:30"

    Then I choose Left Pre Injection Antiseptic "1"
    Then I choose Left Pre Injection Skin Cleanser "2"
    And I tick the Left Pre Injection IOP Lowering Drops checkbox
    Then I choose Left Pre Injection IOP Lowering Drops "1"
    Then I choose Left Drug "7"
    And I enter "2" number of Left injections
    Then I enter Left batch number "123"

    Then I choose Left Injection Given By "11"
    And I enter a Left Injection time of "09:30"

    Then I choose A Right Lens Status of "1"
    And I choose Right Counting Fingers Checked Yes

    And I choose Right IOP Needs to be Checked No
    Then I choose Right Post Injection Drops "1"

    Then I choose A Left Lens Status of "1"
    And I choose Left Counting Fingers Checked Yes


    And I choose Left IOP Needs to be Checked No
    Then I choose Left Post Injection Drops "1"

    And I select Right Complications "6"
    And I select Left Complications "6"
#    6 = Other and opens comment fields

    Then I add Right Complications Comments of "Test Right complication comments"
    Then I add Left Complications Comments of "Test Right complication comments"

    Then I Save the Intravitreal injection and confirm it has been created successfully

    Examples:
      |uname|pwd  |siteName/Number|firmName/Number         |hospNumber|speciality|event                 |
      |admin|admin|Croydon        |Cataract firm (Cataract)|1009465   |Cataract  |Intravitreal injection|



